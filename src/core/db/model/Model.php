<?php
namespace core\db\model;

use core\db\DB;
use core\db\model\ddl\TableDefinition;
use core\db\model\types\Point;
use core\db\query\Query;
use DateTime;

class Model extends \stdClass implements \JsonSerializable {

    use ModelRepository;
    use ModelLoad;

    protected static array $metadata;
    protected static array $listeners;
    protected array $original;

    public bool $exists = false;
    public bool $wasRecentlyCreated = false;

    static function create(mixed ...$values): static {
        $model = (new static(...$values));
        $model->save();
        return $model;
    }

    static function findOrCreate(mixed ...$values): static {
        $query = static::query();
        foreach ($values as $field => $value) $query->eq($field, $value);
        $result = $query->find();
        if (!$result) $result = static::create(...$values);
        return $result;
    }

    static function find(int|string|object $id = null): static {
        return static::query()->find($id);
    }

    static function list(): array {
        return static::query()->list();
    }

    static function query(): Query {
        return DB::table(static::TABLE, 'entity')
            ->fields('entity.*')
            ->map([static::class, 'fromResult'])
            ->onException(function ($e) {

                if ($e->isTableNotFound()) {
                    static::createTable();
                    return $e->retry();
                } else throw $e;
            });
    }

    static function addListener(callable $callback, string $crud = 'c') {
        self::$listeners[static::class][] = [$callback, $crud];
    }

    static function getListeners(): array {
        return self::$listeners[static::class] ?? [];
    }

    static function getMetadata(): ModelMetadata {
        return self::$metadata[static::class] ??= new ModelMetadata(static::class);
    }

    static function fromResult(array $result): static {
        $metadata = static::getMetadata();
        $new = [];
        foreach ($result as $field => $value) {
            $type = $metadata->getFieldType($field);
            $new[$field] = !isset($value) ? null : match ($type) {
                DateTime::class => new \DateTime($value),
                Point::class => Point::fromBinary($value),
                default => $value
            };
            if ($type && is_array($value))
                $new[$field] = (array_filter($value, fn($e) => $e !== null)) ? $type::fromResult($value) : null;
        }
        $model = new static(...$new);

        $model->exists = true;
        return $model;
    }

    static function createTable() {
        $inTransaction = DB::inTransaction();
        $references = static::getMetadata()->getReferences();
        foreach ($references as $ref) $ref::createTable();
        DB::execute(TableDefinition::fromModel(static::class)->getSql());
        if ($inTransaction) DB::beginTransaction();
    }

    function __construct(mixed ...$values) {
        $this->original = $values;
        if ($values) $this->fill(...$values);
    }

    function fill(mixed ...$values) {
        foreach ($values as $field => $value) {
            $this->$field = $value;
        }
    }

    function save() {
        $query = static::query();
        if ($this->exists) {
            if (property_exists($this, 'edited')) $this->{'edited'} = new DateTime();
            $values = $this->getValues(false);
            $query->id($this->getId())->update(...$values);
            $this->triggerEvent('u');
        } else {
            if (property_exists($this, 'created') && !isset($this->created)) $this->{'created'} = new DateTime();
            $values = $this->getValues(false);
            $metadata = static::getMetadata();
            if ($metadata->hasAutoincrementId()) {
                $id = $metadata->getId();
                $this->$id = DB::table(static::TABLE)->insertGetId(...$values);
            } else {
                $query->insert(...$values);
            }
            $this->wasRecentlyCreated = true;
            $this->triggerEvent('c');
        }
        $this->original = $values;
        $this->exists = true;
    }

    function delete() {
        if ($this->exists) {
            static::query()->id($this->getId())->delete();
            $this->triggerEvent('d');
        }
    }

    function getId(): int|string|object {
        $id = static::getMetadata()->getId();
        if (!is_array($id)) return $this->$id;
        $id = array_flip($id);
        foreach ($id as $field => &$value) $value = $this->$field;
        return (object)$id;
    }

    function getValues(bool $includeSubs = true): array {
        $fields = array_keys(static::getMetadata()->getFields());
        $result = [];
        foreach ($fields as $field) {
            if (!isset($this->$field)) continue;
            $value = $this->$field;
            if (!$includeSubs && $value instanceof Model) $value = $value->getId();
            $result[$field] = $value;
        }
        return $result;
    }

    function isDirty(string|array $attributes = null): bool {
        if (!$this->exists) return true;
        if (!$attributes) $attributes = array_keys($this->original);
        elseif (is_string($attributes)) $attributes = [$attributes];

        foreach ($attributes as $attribute) {
            $value = $this->original[$attribute];
            if ($value != ($this->$attribute ?? null)) {
                return true;
            }
        }
        return false;
    }

    function isClean(string|array $attributes = null): bool {
        return !$this->isDirty($attributes);
    }

    protected function triggerEvent(string $operation) {
        if (!isset(self::$listeners[static::class])) return;
        foreach (self::$listeners[static::class] as $listener) {
            if (str_contains($listener[1], $operation)) {
                $callback = $listener[0];
                $callback($this, $operation);
            }
        }
    }

    function jsonSerialize(): mixed {
        $values = $this->getValues();
        foreach ($values as &$value) {
            $value = match (true) {
                $value instanceof DateTime => $value->format('c'),
                default => $value
            };
        }
        return $values;
    }

}
