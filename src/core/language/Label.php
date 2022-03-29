<?php
namespace core\language;

class Label {

    static $data;
    static $defaultKey = 'de';

    static function addFile(string $path) {
        $values = @parse_ini_file($path) ?: [];

        $language = Language::get();
        if ($language) {
            $extensionPos = strrpos($path, '.');
            $nameEndPos = ($path[$extensionPos - 3] === '_') ? $extensionPos - 3 : $extensionPos;
            $languagePath = substr($path, 0, $nameEndPos) . "_$language" . substr($path, $extensionPos);
            $values = (@parse_ini_file($languagePath) ?: []) + $values;
        }

        self::add($values);
    }

    static function add(...$labelLists) {
        self::combine($labelLists, function ($currentValue, $newValue) {
            if (isset($currentValue) && is_array($currentValue)) {
                return array_merge($currentValue, $newValue);
            } else {
                return $newValue;
            }
        });
    }

    static function addBefore(...$labelLists) {
        self::combine($labelLists, function ($currentValue, $newValue) {
            if (isset($currentValue) && is_array($currentValue)) {
                return array_merge($newValue, $currentValue);
            } elseif (!isset($currentValue)) {
                return $newValue;
            }
            return $currentValue;
        });
    }

    private static function combine($labelLists, callable $combiner) {
        if (empty(self::$defaultKey)) {
            self::$defaultKey = Language::get();
        }
        foreach ($labelLists as $labelList) {
            if (empty(self::$data)) {
                self::$data = $labelList;
                continue;
            }
            foreach ($labelList as $label => $newValue) {
                $currentValue = self::$data[$label] ?? null;
                self::$data[$label] = $combiner($currentValue, $newValue);
            }
        }
    }

    static function get($code, ...$params) {
        return self::getOrDefault($code, $code, ...$params);
    }

    static function getOrDefault($code, $default, ...$params) {
        if (empty(self::$data)) {
            return $default;
        }
        if (self::$defaultKey && isset(self::$data[self::$defaultKey])) {
            $label = self::getFromLabels(self::$data[self::$defaultKey], $code);
        }
        if (!isset($label)) {
            $label = self::getFromLabels(self::$data, $code);
        }
        if (empty($label)) {
            return $default;
        }
        if ($params) {
            return sprintf($label, ...$params);
        }
        return $label;
    }

    static function generate($format, ...$values) {
        $result = [];
        foreach ($values as $value) {
            $result[$value] = sprintf($format, $value);
        }
        return $result;
    }

    static function range($from, $to) {
        $range = [];
        for ($i = $from; $i <= $to; $i++) {
            $range[$i] = $i;
        }
        return $range;
    }

    private static function getFromLabels($labels, $code) {
        $label = $labels[$code] ?? null;
        if (empty($label) && !empty($labels)) {
            foreach ($labels as $labelCode => $labelValue) {
                if (!is_string($labelCode)) {
                    continue;
                }
                $strLen = strlen($labelCode);
                $firstChar = $labelCode[0];
                $lastChar = $labelCode[$strLen - 1];
                if ($firstChar == '*' && $lastChar == '*') {
                    if (str_contains($code, substr($labelCode, 1, $strLen - 2))) {
                        return $labelValue;
                    }
                }
                if ($firstChar == '*') {
                    if (str_ends_with($code, substr($labelCode, 1))) {
                        return $labelValue;
                    }
                }
                if ($lastChar == '*') {
                    if (str_starts_with($code, substr($labelCode, 0, $strLen - 1))) {
                        return $labelValue;
                    }
                }
            }
        }
        return $label;
    }

}
