<?php
namespace core\db\model;

use core\db\model\attributes\{Column, HasMany, HasOne, Reference};

class ModelMetadata {

    private string $name;
    private array $id = [];
    private array $fields = [];

    function __construct(string $class) {
        $rClass = new \ReflectionClass($class);
        $this->name = $rClass->getShortName();
        $rProperties = $rClass->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($rProperties as $rProperty) {
            if ($rProperty->isStatic()) {
                continue;
            }
            $name = $rProperty->getName();
            $field = [];
            $rAttributes = $rProperty->getAttributes();
            foreach ($rAttributes as $rAttribute) {
                $attr = $rAttribute->newInstance();
                if ($attr instanceof Column) {
                    $rTypes = $rProperty->getType();
                    if ($rTypes instanceof \ReflectionUnionType) {
                        $field['types'] = array_map(fn($rT) => $rT->getName(), $rTypes->getTypes());
                        $field['type'] = $field['types'][0];
                        $field['isPrimitive'] = $rTypes->getTypes()[0]->isBuiltin();
                    } else {
                        $field['type'] = $rTypes?->getName();
                        $field['isPrimitive'] = $rTypes->isBuiltin();
                    }
                    if ($attr->primaryKey) $this->id['fields'][] = $name;
                    if ($attr->autoIncrement) $this->id['autoIncrement'] = true;
                } elseif ($attr instanceof Reference) {
                    $field['reference'] = $field['type'];
                } elseif ($attr instanceof HasMany) {
                    $field['hasMany'] = $rAttribute->newInstance()->modelClass;
                } elseif ($attr instanceof HasOne) {
                    $field['hasOne'] = $rAttribute->newInstance()->modelClass ?? $field['type'];
                }
            }
            if ($field) $this->fields[$name] = $field;
        }
    }

    function hasAutoincrementId(): bool {
        return isset($this->id['autoIncrement']);
    }

    function getName(): string {
        return $this->name;
    }

    function getId(): string|array {
        $id = $this->id['fields'];
        return count($id) > 1 ? $id : $id[0];
    }

    function getFields(): array {
        return $this->fields;
    }

    public function getField(string $field): array|null {
        return $this->fields[$field] ?? null;
    }

    function getFieldType(string $field): mixed {
        return $this->fields[$field]['type'] ?? null;
    }

    function getReferences(): array {
        $references = [];
        foreach ($this->fields as $field)
            if ($ref = $field['reference'] ?? null) $references[] = $ref;
        return $references;
    }

    function isPrimitive(string $field): bool {
        return $this->fields[$field]['isPrimitive'] ?? false;
    }

    function hasMany(string $field): bool {
        return $this->fields[$field]['hasMany'] ?? false;
    }

}
