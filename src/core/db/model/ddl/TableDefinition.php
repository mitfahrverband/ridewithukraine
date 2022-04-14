<?php
namespace core\db\model\ddl;

use core\db\DB;
use core\db\model\attributes\{Column, Enum, Geometry, Index, Reference, Unique};
use core\db\model\Model;
use core\db\model\types\{Point, Polygon};
use core\db\query\Query;
use DateTime;
use ReflectionClass;
use RuntimeException;

class TableDefinition {

    protected string $name;
    protected array $columns = [];

    static function fromModel(string $class): self {
        $table = $class::TABLE;
        if (!$table) {
            throw new RuntimeException("Table not found in $class");
        }
        $def = new TableDefinition();
        $def->name = $table;
        $ref = new ReflectionClass($class);
        $properties = $ref->getProperties(\ReflectionProperty::IS_PUBLIC);
        $classColumns = [];
        foreach ($properties as $property) {
            $column = $def->generateColumnDefinition($property);
            if (!$column) continue;
            $classColumns[$property->class][$column->name] = $column;
        }
        foreach (array_reverse($classColumns) as $columns) {
            $def->columns += $columns;
        }
        return $def;
    }

    protected function generateColumnName(\ReflectionProperty $property): string {
        return Query::toSnakeCase($property->getName());
    }

    protected function generateColumnDefinition(\ReflectionProperty $property): object|null {
        if ($property->isStatic() || $property->class === Model::class) return null;
        $type = $property->getType();
        $class = null;
        if ($type instanceof \ReflectionUnionType) {
            foreach ($type->getTypes() as $partType) {
                $partTypeName = $partType->getName();
                if (!$partType->isBuiltin()) {
                    $class = $partTypeName;
                } else {
                    $type = $partType;
                    break;
                }
            }
        }

        $attributes = $property->getAttributes();
        foreach ($attributes as $attribute) {
            $attr = $attribute->newInstance();
            if ($attr instanceof Column) {
                $column = (object)[];
                $column->name = $this->generateColumnName($property);
                $column->phpType = $type->getName();
                $column->unsigned = $attr->unsigned ?? ((bool)$class);

                if ($attr->length) {
                    $column->length = $attr->length;
                }

                $enumValues = $this->enumValues($property);
                if ($enumValues) {
                    $column->enumValues = $enumValues;
                }

                $column->dbType = $this->getDbType($attr->type ?? $column->phpType, $column);

                $default = $this->getDefault($attr->default ?? $property->getDefaultValue(), $column);
                if (isset($default)) {
                    $column->default = $default;
                }

                if ((isset($attr->notNull) && $attr->notNull) ||
                    (!isset($attr->notNull) && !$property->getType()->allowsNull())) {
                    $column->notNull = true;
                }

                if (isset($attr->autoIncrement) && $attr->autoIncrement) {
                    $column->autoIncrement = true;
                }

                if ($attr->primaryKey) {
                    $column->primaryKey = true;
                }
                if (isset($attr->index)) {
                    $name = is_string($attr->index) ? $attr->index : null;
                    $column->index = new Index($name);
                }

            } elseif (isset($column)) {
                if ($attr instanceof Reference) {
                    $table = $attr->table ?: $class::TABLE;
                    $column->reference = (object)array_filter([
                        'table' => $table,
                        'column' => $attr->column,
                        'onDelete' => $attr->onDelete,
                        'onUpdate' => $attr->onUpdate,
                    ]);
                } elseif ($attr instanceof Index) {
                    $column->index = $attr;
                } elseif ($attr instanceof Unique) {
                    $column->unique ??= [];
                    $column->unique[] = $attr->name ?? true;
                } elseif ($attr instanceof Geometry) {
                    $column->geometry = (object)[
                        'srid' => $attr->srid
                    ];
                }
            }
        }
        return $column ?? null;
    }

    public function getUniqueKeyColumns(): array {
        $keys = [];
        foreach ($this->columns as $column) {
            if (!isset($column->unique)) {
                continue;
            }
            foreach ($column->unique as $key) {
                $keys[$key][] = $column->name;
            }
        }
        return array_map(fn($k) => join(',', $k), $keys);
    }

    protected function enumValues(\ReflectionProperty $property): array|null {
        $attributes = $property->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getName() === Enum::class) {
                return $attribute->newInstance()->values;
            }
        }
        return null;
    }

    protected function getDefault(string|null $default, object $column): string|null {
        if (isset($default)) {
            if ($column->phpType === 'string') {
                return "'$default'";
            } elseif ($column->phpType === 'bool') {
                return $default ? 1 : 0;
            }
        }
        return $default;
    }

    protected function getDbType(string $type, object $column): string {
        if (isset($column->enumValues)) {
            $enumValues = array_map(fn($v) => "'$v'", $column->enumValues);
            $enumValues = join(',', $enumValues);
            return "enum($enumValues)";
        }
        $result = match ($type) {
            Column::DATETIME, DateTime::class => 'datetime',
            Column::JSON => 'json',
            'string' => 'varchar(' . ($column->length ?? 100) . ')',
            'bool' => 'int',
            Point::class => 'point',
            Polygon::class => 'polygon',
            default => $type
        };
        if ($column->unsigned ?? false) $result .= ' unsigned';
        return $result;
    }

    function getSql(): string {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->name} (\n";
        $definitions = [];
        foreach ($this->columns as $column) {
            $column->name = Query::escapeColumn($column->name);
            $definition = "$column->name $column->dbType";
            if (isset($column->default)) {
                $definition .= " DEFAULT $column->default";
            }
            if (isset($column->notNull)) {
                $definition .= " NOT NULL";
            }
            if (isset($column->autoIncrement) && $column->autoIncrement) {
                $definition .= " AUTO_INCREMENT";
            }
            if (isset($column->geometry) && (DB::$type & DB::MySql)) {
                $definition .= ' srid ' . $column->geometry->srid;
            }
            $definitions[] = $definition;

            if (isset($column->reference)) {
                $ref = $column->reference;
                $onDelete = isset($ref->onDelete) ? " ON DELETE $ref->onDelete" : '';
                $onUpdate = isset($ref->onUpdate) ? " ON UPDATE $ref->onUpdate" : '';
                $definitions[] = "FOREIGN KEY ($column->name) REFERENCES $ref->table ($ref->column)$onUpdate$onDelete";
            }
        }

        $primaryKey = array_filter($this->columns, fn($c) => $c->primaryKey ?? null);
        $primaryKey = array_map(fn($c) => $c->name, $primaryKey);
        $primaryKey = join(',', $primaryKey);
        if ($primaryKey) {
            $definitions[] = "PRIMARY KEY($primaryKey)";
        }

        $uniques = $this->getUniqueKeyColumns();
        foreach ($uniques as $unique) {
            $definitions[] = "UNIQUE($unique)";
        }

        $indexColumns = array_filter($this->columns, fn($c) => isset($c->index));
        foreach ($indexColumns as $c) $indices[$c->index->name ?? $c->name][] = $c;
        foreach ($indices ?? [] as $name => $columns) {
            $type = str_starts_with($name, 'text') ? 'FULLTEXT' : 'INDEX';
            if (isset($columns[0]->geometry)) {
                $type = 'SPATIAL';
            }
            $columns = array_map(function ($c) {
                $name = $c->name;
                if ($c->index->length) {
                    $name .= "({$c->index->length})";
                }
                return $name;
            }, $columns);
            $columns = join(',', $columns);
            $definitions[] = "$type($columns)";
        }

        $sql .= join(",\n", $definitions);
        $sql .= "\n)";
        return $sql;
    }

}
