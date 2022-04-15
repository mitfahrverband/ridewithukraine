<?php
namespace core\db\model;

use core\db\query\Query;

trait ModelLoad {

    static function load(&$models, string $path = null, string ...$fields) {
        if (!$models) return;
        if (is_array($models)) $modelArray = &$models;
        else $modelArray = [&$models];

        $path ??= Query::toSnakeCase(static::getMetadata()->getName());

        // Collect references
        $hasMany = false;
        foreach ($modelArray as $model) {
            if ($model::getMetadata()->hasMany($path)) {
                $model->$path = [];
                $t = $model;
                $hasMany = true;
            } elseif (isset ($model->$path)) {
                $t = $model->$path;
            } else {
                continue;
            }
            $refs[$t instanceof Model ? $t->getId() : $t][] = &$model->$path;
        }
        if (!isset($refs)) return;

        // Query
        if ($hasMany) {
            $field = $model::getMetadata()->getName();
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
        foreach ($fetchedEntities as $model) {
            $id = $hasMany ? $model->$field : $model->getId();
            foreach ($refs[$id] ?? [] as &$ref)
                if (is_array($ref)) {
                    if (isset($removeMappingField)) unset($model->$field);
                    $ref[] = $model;
                } else {
                    $ref = $model;
                }
        }
    }

}
