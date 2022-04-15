<?php
namespace core\http\request;

class Request {

    private static string $body;

    static function param(string $name, bool $throw = false): RequestParam {
        $phpName = str_replace('.', '_', $name);
        $value = $_POST[$phpName] ?? $_GET[$phpName] ?? null;
        return new RequestParam($name, $value, $throw);
    }

    static function paramUrl(string $name, bool $throw = false): RequestParam {
        $value = $_GET[str_replace('.', '_', $name)] ?? null;
        return new RequestParam($name, $value, $throw);
    }

    static function paramJson(string $path, bool $throw = false): RequestParam {
        $names = explode('.', $path);
        $value = json_decode(self::getBody(), flags: $throw ? JSON_THROW_ON_ERROR : 0);
        foreach ($names as $name) $value = $value->$name ?? null;
        return new RequestParam($path, $value, $throw);
    }

    static function getBody() {
        return self::$body ??= file_get_contents('php://input');
    }

}
