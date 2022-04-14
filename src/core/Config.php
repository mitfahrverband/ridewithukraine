<?php
namespace core;

class Config {

    const FILE = __DIR__ . '/../config.ini';

    public static array $config;
    public static string $root;
    public static string $documentRoot;

    static function load() {
        $path = getenv('config') ?: self::FILE;
        $config = @parse_ini_file($path, true);
        self::$config = $config ?: [];
        self::$root = self::get('server', 'root', __DIR__ . '/..');
        self::$documentRoot = self::get('server', 'documentRoot', self::$root . '/public');
    }

    static function get($section = null, $key = null, $default = null) {
        $result = self::$config;
        if (isset($section)) {
            $result = $result[$section] ?? null;
            if (isset($result) && isset($key)) {
                $result = $result[$key] ?? null;
            }
        }
        return $result ?? $default;
    }

    static function require($section = null, $key = null) {
        $result = self::get($section, $key);
        if (!isset($result)) {
            throw new \RuntimeException("config entry [$section]$key not found");
        }
        return $result;
    }

    static function set(?string $section, string $key, mixed $value, bool $override = false) {
        if (isset($section)) {
            $ref = &self::$config[$section][$key];
        } else {
            $ref = &self::$config[$key];
        }
        if (!isset($ref) || $override) {
            $ref = $value;
        }
    }

    static function getJobs(): array {
        return [];
    }

    static function getRoutes(): array {
        return [];
    }

}
