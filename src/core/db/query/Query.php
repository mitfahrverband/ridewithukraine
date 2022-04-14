<?php
namespace core\db\query;

use core\db\DB;
use core\db\DBException;
use DateTime;

class Query {

    private string $fields = '*';
    private string $tableWithAlias;
    private string $join = '';
    private string $where = '';
    private string $orderBy = '';
    private bool $or = false;
    private bool $not = false;
    private array $whereParams = [];
    private array $joinParams = [];
    private array $mappers;
    private mixed $exceptionHandler;
    private array $nameCache = [];

    function __construct(
        public string  $table,
        public ?string $as = null,
    ) {
        $this->tableWithAlias = $as ? "$table $as" : $table;
    }

    function fields(string|array ...$fields) {
        $this->fields = '*';
        return $this->addFields(...$fields);
    }

    function addFields(string|array ...$fields) {
        foreach ($fields as $key => &$field) {
            if (is_int($key)) continue;
            foreach ($field as &$value) {
                $dotPos = strrpos($value, '.');
                $column = $dotPos === false ? "$key.$value" : $value;
                $name = $dotPos === false ? $value : substr($value, $dotPos + 1);
                $value = "$column AS {$key}»" . $name;
            }
            $field = join(',', $field);
        }
        $fields = join(',', $fields);
        $this->fields = ($this->fields === '*') ? $fields : "$this->fields,$fields";
        return $this;
    }

    function join(string $sql, mixed ...$params) {
        return $this->joinRaw("JOIN $sql", ...$params);
    }

    function joinLeft(string $sql, mixed ...$params) {
        return $this->joinRaw("LEFT JOIN $sql", ...$params);
    }

    function joinRight(string $sql, mixed ...$params) {
        return $this->joinRaw("RIGHT JOIN $sql", ...$params);
    }

    function joinRaw(string $sql, mixed ...$params) {
        $this->join .= "\n$sql";
        array_push($this->joinParams, ...$params);
        return $this;
    }

    function id(int|string|object|array $id, string $alias = null): self {
        $alias ??= $this->as ?? null;
        $alias = isset($alias) ? "$alias." : '';
        if (is_array($id)) {
            return $this->where(function () use ($id) {
                foreach ($id as $id1) self::id($id1)->or();
            });
        }
        if (!is_object($id)) {
            return $this->eq($alias . 'id', $id)->or();
        }
        foreach ((array)$id as $field => $value) $this->eq($alias . $field, $value);
        return $this;
    }

    function eq(string $field, mixed $value) {
        return $this->exp($field, '=', $value);
    }

    function in(string $field, array $values) {
        $field = $this->convertNames([$field])[0];
        $withoutNullValues = array_filter($values, fn($v) => $v !== null);
        $in = function ($q) use ($field, $withoutNullValues) {
            $placeholders = static::placeholders(count($withoutNullValues));
            return $q->where("$field IN ($placeholders)", ...$withoutNullValues);
        };
        if (count($withoutNullValues) === count($values)) $in($this);
        else $this->where(fn($q) => $in($q)->or()->eq($field, null)); // (field IN (?,?) OR field IS null)
        return $this;
    }

    function exp(string $field, string $operator, mixed $value): self {
        $placeholder = self::placeholders([$value]);
        if (!isset($value)) {
            $operator = 'IS';
            $placeholder = 'null';
        }
        $field = $this->convertNames([$field])[0];
        $sql = "$field $operator $placeholder";
        return $this->where($sql, $value);
    }

    function where(string|callable $sql, mixed ...$params): self {
        if (!$this->where) $this->where = "\nWHERE ";
        elseif (!str_ends_with($this->where, '(')) {
            $this->where .= $this->or ? ' OR ' : ' AND ';
            $this->or = false;
        }
        if ($this->not) {
            $this->where .= 'NOT ';
            $this->not = false;
        }
        if (is_callable($sql)) {
            $this->where .= '(';
            $sql($this);
            $this->where .= ')';
        } else {
            if (stripos($sql, 'OR') !== false) { // "or" and "OR"
                $sql = ("($sql)");
            }
            $this->where .= $sql;
        }
        array_push($this->whereParams, ...$this->convertValues($params));
        return $this;
    }

    function or(): self {
        $this->or = true;
        return $this;
    }

    function and(): self {
        $this->or = false;
        return $this;
    }

    function not(): self {
        $this->not = !$this->not;
        return $this;
    }

    function orderBy(string $field, string $direction = 'asc'): self {
        $field = static::convertNames([$field])[0];
        if (!$this->orderBy)
            $this->orderBy = "\nORDER BY $field $direction";
        else
            $this->orderBy .= ", $field $direction";
        return $this;
    }

    function map(callable $mapper, bool $before = false) {
        $this->mappers ??= [];
        if ($before) array_unshift($this->mappers, $mapper);
        else $this->mappers[] = $mapper;
        return $this;
    }

    function onException(callable $function) {
        $this->exceptionHandler = $function;
        return $this;
    }

    function find(int|string|object $id = null): mixed {
        if (isset($id)) $this->id($id);
        $sql = $this->getSelect();
        $result = $this->handleException(fn() => DB::queryRow($sql, ...$this->getParams()));
        return $this->convertResult($result);
    }

    function list(): array {
        $sql = $this->getSelect();
        $rows = $this->handleException(fn() => DB::queryRows($sql, ...$this->getParams()));
        $results = [];
        foreach ($rows as $result) {
            $results[] = $this->convertResult($result);
        }
        return $results;
    }

    function count() {
        $sql = "SELECT COUNT(*) FROM ({$this->getSelect()}) c";
        return $this->handleException(fn() => DB::queryValue($sql, ...$this->getParams()));
    }

    function exists() {
        $sql = "SELECT 1 FROM ({$this->getSelect()}) c";
        return $this->handleException(fn() => DB::queryValue($sql, ...$this->getParams()) === 1);
    }

    function insert(mixed ...$values) {
        $isSingle = is_string(array_key_first($values));
        if ($isSingle) {
            $fields = array_keys($values);
            $placeholders = static::placeholders($values);
        } else {
            if (!is_string(array_key_first($values[0]))) throw new \RuntimeException('invalid values');
            $fields = [];
            foreach ($values as $batch)
                foreach ($batch as $batchField => $batchValue)
                    $fields[] = $batchField;
            $fields = array_flip(array_flip($fields));
            $batchValues = [];
            $placeholders = [];
            foreach ($values as $batch) {
                $placeholders[] = static::placeholders($batch, $fields);
                foreach ($fields as $field)
                    $batchValues[] = $batch[$field] ?? null;
            }
            $placeholders = join('),(', $placeholders);
            $values = $batchValues;
        }
        $fields = join(',', $this->convertNames($fields));
        $values = $this->convertValues($values);
        $this->handleException(
            fn() => DB::execute("INSERT INTO $this->table ($fields) VALUES ($placeholders)", ...array_values($values))
        );
    }

    function insertGetId(mixed ...$values): int {
        $this->insert(...$values);
        return DB::lastId();
    }

    function update(mixed ...$values) {
        $sets = [];
        $fields = $this->convertNames(array_keys($values));
        foreach ($fields as $field) {
            $sets[] = "$field=?";
        }
        $sets = join(',', $sets);
        $values = array_merge($this->convertValues(array_values($values)), $this->getParams());
        $this->handleException(
            fn() => DB::execute("UPDATE $this->tableWithAlias SET $sets" . $this->where, ...$values)
        );
    }

    function upsert(array $values, array|bool $update = true) {
        $fields = $this->convertNames(array_keys($values));
        $placeholders = static::placeholders(count($values));
        $values = $this->convertValues($values);

        $update = is_bool($update) ? $fields : $update;
        $sets = [];
        foreach ($update as $field) {
            $sets[] = "$field=VALUES($field)";
        }

        $sets = join(',', $sets);
        $fields = join(',', $fields);
        $sql = "INSERT INTO $this->table ($fields) VALUES ($placeholders) ON DUPLICATE KEY UPDATE $sets";
        $this->handleException(fn() => DB::execute($sql, ...array_values($values)));
    }

    function delete() {
        $this->handleException(
            fn() => DB::execute("DELETE FROM $this->table" . $this->where, ...$this->getParams())
        );
    }

    function getSelect(): string {
        $sql = "SELECT $this->fields FROM $this->tableWithAlias";
        $sql .= $this->join . $this->where . $this->orderBy;
        return $sql;
    }

    function getParams(): array {
        return array_merge($this->joinParams, $this->whereParams);
    }

    function convertNames(array $names): array {
        foreach ($names as &$name) {
            $name = static::toSnakeCase($name);
            $name = static::escapeColumn($name);
        }
        return $names;
    }

    protected function convertValues(array $values): array {
        foreach ($values as $key => &$value) {
            if ($value instanceof RawValue) {
                unset($values[$key]);
                continue;
            }
            $value = match (true) {
                $value instanceof DateTime => $value->format('Y-m-d H:i:s'),
                is_bool($value) => (int)$value,
                is_object($value) => "$value",
                default => $value
            };
        }
        return array_values($values);
    }

    protected function convertResult(array|bool $result): object|null {
        if (!$result) return null;
        $mappedResult = [];
        foreach ($result as $field => $value) {
            $field = $this->nameCache[$field] ??= static::toCamelCase($field);
            $elements = explode('»', $field);
            $temp = &$mappedResult;
            foreach ($elements as $element) {
                if (!is_array($temp)) $temp = [];
                $temp = &$temp[$element];
            }
            $temp = $value;
        }
        if (isset($this->mappers)) {
            foreach ($this->mappers as $mapper) $mappedResult = $mapper($mappedResult);
        }
        return (object)$mappedResult;
    }

    protected function handleException(callable $function): mixed {
        try {
            return $function();
        } catch (DBException $e) {
            if (isset($this->exceptionHandler)) return ($this->exceptionHandler)($e);
            else throw $e;
        }
    }

    static function placeholders(int|array $params, array $fields = null) {
        if (is_int($params)) return substr(str_repeat('?,', $params), 0, -1);
        if ($fields) {
            foreach ($fields as $field) $newParams[] = $params[$field] ?? null;
            $params = $newParams;
        }
        foreach ($params as $param) {
            $placeholders[] = $param instanceof RawValue ? $param->getSql() : '?';
        }
        return join(',', $placeholders ?? []);
    }

    static function toSnakeCase($name) {
        return strtolower(preg_replace('/\B([A-Z])/', '_$1', $name));
    }

    static function toCamelCase($name) {
        return preg_replace_callback('/_\w/', fn($gs) => strtoupper($gs[0][1]), $name);
    }

    static function escapeColumn(string $column): string {
        return in_array($column, ['key', 'keys', 'group'], true) ? "`$column`" : $column;
    }

}
