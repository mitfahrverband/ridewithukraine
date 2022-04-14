<?php
namespace core\db\model;

use core\db\query\Query;

trait ModelLoad {

    static function load(&$entities, string $path = null, string ...$fields) {
        if (!$entities) return;
        if (is_array($entities)) $entityArray = &$entities;
        else $entityArray = [&$entities];

        $path ??= Query::toSnakeCase(static::getMetadata()->getName());

        // Collect references
        $hasMany = false;
        foreach ($entityArray as $entity) {
            if ($entity::getMetadata()->hasMany($path)) {
                $entity->$path = [];
                $t = $entity;
                $hasMany = true;
            } elseif (isset ($entity->$path)) {
                $t = $entity->$path;
            } else {
                continue;
            }
            $refs[$t instanceof Model ? $t->getId() : $t][] = &$entity->$path;
        }
        if (!isset($refs)) return;

        // Query
        if ($hasMany) {
            $field = $entity::getMetadata()->getName();
        } else {
            $field = static::getMetadata()->getName();
        }
        $field = lcfirst(Query::toCamelCase($field));
        $query = static::query()->in($field, array_keys($refs));
        if ($fields) {
            if (!in_array($field, $fields)) {
                $fields[] = $field;
                $removeMappingField = true;
            }
            $query->fields(...$fields);
        }
        $fetchedEntities = $query->list();

        // Set results
        foreach ($fetchedEntities as $entity) {
            $id = $hasMany ? $entity->$field : $entity->getId();
            foreach ($refs[$id] ?? [] as &$ref)
                if (is_array($ref)) {
                    if (isset($removeMappingField)) unset($entity->$field);
                    $ref[] = $entity;
                } else {
                    $ref = $entity;
                }
        }
    }

}
