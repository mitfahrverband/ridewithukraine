<?php
namespace core\http;

use core\Config;

class Url {

    static function addParams(string $url, string ...$name_values): string {
        if (!$name_values) return $url;
        $params = [];
        foreach ($name_values as $name => $value)
            $params[] = urlencode($name) . '=' . urlencode($value);
        $url .= str_contains($url, '?') ? '&' : '?';
        $url .= join('&', $params);
        return $url;
    }

    // This requires a redirect to the resource e.g. in .htaccess
    // RewriteEngine On
    // RewriteRule ^(.*)\.[\d]{1,}\.(css|js)$ $1.$2 [L]
    static function version(string $url, string $version = null): string {
        if (!Config::get(self::class, 'versioning', true)) return $url;
        if (!$version) $version = strval(filemtime(Config::$documentRoot . $url));
        $lastDot = strrpos($url, '.');
        return substr_replace($url, ".$version", $lastDot, 0);
    }

}
