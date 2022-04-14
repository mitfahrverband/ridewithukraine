<?php
namespace core\db;

use core\Config;
use core\Log;
use PDO;
use PDOStatement;
use RuntimeException;

class DB {

    const MySql = 1;
    const MariaDB = 2;
    static int $type = self::MySql;

    static PDO $pdo;

    static function table(string $table, string $as = null): \core\db\query\Query {
        return new \core\db\query\Query($table, $as);
    }

    static function query($sql, ...$params): false|PDOStatement {
        try {
            if (!$params) {
                $result = self::$pdo->query($sql);
            } else {
                $stmt = self::$pdo->prepare($sql);
                $stmt->execute($params);
                $result = $stmt;
            }
            if (Log::isDebug()) {
                Log::debug("\n$sql\n" . json_encode($params));
            }
            return $result;
        } catch (\Exception $e) {
            throw new DBException($sql, $params, $e, fn() => static::query($sql, ...$params));
        }
    }

    static function queryObject($sql, $class = \stdClass::class, ...$params): ?object {
        $stmt = self::query($sql, ...$params);
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $class);
        $object = $stmt->fetch();
        return $object ?: null;
    }

    static function queryObjects($sql, $class = \stdClass::class, ...$params): array {
        $stmt = self::query($sql, ...$params);
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $class);
        $objs = array();
        while ($obj = $stmt->fetch()) {
            $objs[] = $obj;
        }
        return $objs;
    }

    static function exists($sql, ...$params): bool {
        $stmt = self::query($sql, ...$params);
        return $stmt->rowCount() > 0;
    }

    static function queryValue($sql, ...$params): mixed {
        $stmt = self::query($sql, ...$params);
        return $stmt->fetchColumn();
    }

    static function queryValues($sql, ...$params): array {
        $stmt = self::query($sql, ...$params);
        $array = [];
        while (($single = $stmt->fetchColumn()) !== false) {
            $array[] = $single;
        }
        return $array;
    }

    static function queryRow($sql, ...$params): array|false {
        $stmt = self::query($sql, ...$params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    static function queryRows(string $sql, mixed ...$params): array|false {
        $stmt = self::query($sql, ...$params);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $results = [];
        while ($result = $stmt->fetch()) {
            $results[] = $result;
        }
        return $results;
    }

    static function execute($sql, ...$params): int {
        if (!is_string($sql)) {
            $stmt = $sql;
        }
        try {
            if (!$params && !isset($stmt)) {
                $result = self::$pdo->exec($sql);
            } else {
                $stmt ??= self::$pdo->prepare($sql);
                $stmt->execute($params);
                $result = $stmt->rowCount();
            }
            if (Log::isDebug()) {
                Log::debug("\n$sql\n" . json_encode($params) . "\nAffected rows: $result");
            }
            return $result;
        } catch (\Exception $e) {
            throw new DBException($sql, $params, $e, fn() => static::execute($sql, ...$params));
        }
    }

    static function beginTransaction() {
        self::$pdo->beginTransaction();
    }

    static function inTransaction(): bool {
        return self::$pdo->inTransaction();
    }

    static function commit() {
        self::$pdo->commit();
    }

    static function rollback() {
        self::$pdo->rollBack();
    }

    static function lastId() {
        $lastInsertId = self::$pdo->lastInsertId();
        return is_numeric($lastInsertId) ? intval($lastInsertId) : $lastInsertId;
    }

    static function connect($config) {
        if ($type = $config['type'] ?? null) {
            if (!defined(self::class . '::' . $type))
                throw new RuntimeException("Unknown DB type '$type'. Check config.");
            self::$type = constant(self::class . '::' . $type);
        }

        self::$pdo = new PDO(
            $config['dsn'],
            $config['user'] ?? null,
            $config['password'] ?? null,
            [
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

}

if ($dbConfig = Config::require(DB::class)) {
    DB::connect($dbConfig);
    unset($dbConfig);
}
