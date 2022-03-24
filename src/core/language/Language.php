<?php
namespace core\language;

class Language {

    static array $languages = [
        'de',
        'en'
    ];

    static string $preferred;

    static function get(): string|null {
        return self::fromAttribute() ?? self::$preferred ?? self::fromHeader() ?? self::$languages[0] ?? null;
    }

    static function set(string $lang) {
        if (self::isValid($lang)) self::$preferred = $lang;
    }

    static function fromAttribute(): string|null {
        $lang = $_GET['lang'] ?? null;
        if (!$lang || !self::isValid($lang)) return null;
        return $lang;
    }

    static function fromHeader(): string|null {
        $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null;
        if (!$lang || !is_string($lang)) return null;
        $lang = substr($lang, 0, 2);
        if (!self::isValid($lang)) return null;
        return $lang;
    }

    private static function isValid(mixed $lang): bool {
        return is_string($lang) && in_array($lang, self::$languages ?? [], true);
    }

}
