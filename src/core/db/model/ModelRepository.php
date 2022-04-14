<?php
namespace core\db\model;

use core\db\query\Query;
use RuntimeException;

trait ModelRepository {

    static function __callStatic($name, $arguments) {
        if (str_starts_with($name, 'list')) {
            $name = substr($name, 4);
            $valueColumn = self::_getColumn($name);
            $query = self::_query($name, $arguments);
            if ($valueColumn) {
                $query->fields("entity.$valueColumn");
                return array_map(fn($e) => $e->$valueColumn, $query->list());
            }
            return $query->list();
        } elseif (str_starts_with($name, 'find')) {
            $name = substr($name, 4);
            $valueColumn = self::_getColumn($name);
            $query = self::_query($name, $arguments);
            if ($valueColumn) {
                $query->fields("entity.$valueColumn");
                return $query->find()?->$valueColumn;
            }
            return $query->list();
        } elseif (str_starts_with($name, 'exists')) {
            $name = substr($name, 6);
            return self::_query($name, $arguments)->exists();
        } elseif (str_starts_with($name, 'count')) {
            $name = substr($name, 5);
            return self::_query($name, $arguments)->count();
        } else {
            throw new RuntimeException("could not parse method \"$name\"");
        }
    }

    private static function _query($string, $arguments): Query {
        $columns = self::_getByColumns($string);
        $or = str_contains($columns, '_or_');
        $columns = explode($or ? '_or_' : '_and_', $columns);
        $query = static::query();
        if (isset($columns) && $arguments) {
            foreach ($columns as $key => $column) {
                if ($or) $query->or();
                self::_setToStmt($query, $column, $arguments[$key] ?? null);
            }
        }
        return $query;
    }

    private static function _setToStmt($stmt, $column, $value) {
        $not = str_starts_with($column, 'not_');
        $column = $not ? substr($column, 4) : $column;
        $prefixedColumn = "entity.$column";
        if ($column === 'id') {
            $stmt->id($value);
            return;
        }
        if ($not) $stmt->not();
        if (is_array($value))
            $stmt->in($prefixedColumn, ...$value);
        else
            $stmt->eq($prefixedColumn, $value);
    }

    private static function _getColumn(&$string) {
        $pos = strpos($string, 'By');
        if ($pos === false) {
            $column = $string;
            $string = '';
        } else {
            $column = substr($string, 0, $pos);
            $string = substr($string, $pos);
        }
        return Query::toSnakeCase($column);
    }

    private static function _getByColumns($string, $explode = false) {
        if (str_starts_with($string, 'By')) {
            $columns = substr($string, 2);
            $columns = Query::toSnakeCase($columns);
            if ($explode) {
                $columns = explode('_and_', $columns);
            }
        }
        return $columns ?? null;
    }

}
