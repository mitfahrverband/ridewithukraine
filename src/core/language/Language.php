<?php
namespace core\language;

class Language {

    private static string $preferred;

    static function set(string $lang) {
        if (self::isValid($lang)) {
            self::setCookie($lang);
            self::$preferred = $lang;
        }
    }

    static function get(): string|null {
        $attribute = self::getAttribute();
        if ($attribute) self::setCookie($attribute);
        return $attribute ?? self::$preferred ?? self::getCookie() ?? self::getHeader() ?? null;
    }

    static function getAttribute(): string|null {
        $lang = $_GET['lang'] ?? null;
        if (!$lang || !self::isValid($lang)) return null;
        return strtolower($lang);
    }

    static function getCookie(): string|null {
        $lang = $_COOKIE['lang'] ?? null;
        if (self::isValid($lang)) return strtolower($lang);
        return null;
    }

    static function setCookie(string $lang) {
        if (self::isValid($lang)) {
            setcookie('lang', strtolower($lang), strtotime('+1 year'));
        }
    }

    static function getHeader(): string|null {
        $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null;
        if (!$lang || !is_string($lang)) return null;
        $lang = substr($lang, 0, 2);
        if (self::isValid($lang)) return strtolower($lang);
        return null;
    }

    private static function isValid(mixed $lang): bool {
        return $lang && is_string($lang) && strlen($lang) === 2;
    }

}
