<?php
namespace core;

use Exception;
use Throwable;

class Log {

    const LEVELS = [
        E_ALL => 'DEBUG',
        E_NOTICE => 'INFO',
        E_WARNING => 'WARNING',
        E_ERROR => 'ERROR',
        0 => 'OFF',
    ];

    const PATH = __DIR__ . '/../logs';

    private static string $file = 'app.log';
    private static int $level = E_NOTICE;

    static function setFile($file) {
        self::$file = $file;
    }

    static function log(int $level, mixed $value, string $file = null, int $line = null) {
        if (self::$level >= $level) {
            self::logEntry(self::LEVELS[$level] ?? E_WARNING, $value, $file, $line);
        }
    }

    static function debug($value) {
        if (self::$level >= E_ALL) {
            self::logEntry('DEBUG', $value);
        }
    }

    static function info($value) {
        if (self::$level >= E_NOTICE) {
            self::logEntry('INFO', $value);
        }
    }

    static function error($value) {
        if (self::$level >= E_ERROR) {
            self::logEntry('ERROR', $value);
        }
    }

    static function isDebug(): bool {
        return self::$level == E_ALL;
    }

    static function setDebug() {
        self::$level = E_ALL;
    }

    static function isInfo(): bool {
        return self::$level == E_NOTICE;
    }

    static function setInfo() {
        self::$level = E_NOTICE;
    }

    static function isError(): bool {
        return self::$level == E_ERROR;
    }

    static function setError() {
        self::$level = E_ERROR;
    }

    static function isOff(): bool {
        return self::$level == 0;
    }

    static function setOff() {
        self::$level = 0;
    }

    private static function varDumpToString($var): string {
        ob_start();
        var_dump($var);
        return rtrim(ob_get_clean());
    }

    private static function logEntry($level, $value, $file = null, $line = null) {
        if (!file_exists(self::PATH)) {
            mkdir(self::PATH);
        }
        $fileHandle = fopen(self::PATH . '/' . self::$file, 'a');
        if ($fileHandle) {
            if ($value instanceof Throwable) {
                $file ??= $value->getFile();
                $line ??= $value->getLine();
                $value = "$value";
            }
            if (is_null($value) || is_object($value) || is_array($value) || is_bool($value)) {
                $value = self::varDumpToString($value);
            }
            $date = date('d.m.Y H:i:s');
            $stackFrame = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
            $file = basename($file ?? $stackFrame['file']);
            $line = $line ?? $stackFrame['line'];
            fwrite($fileHandle, "$date $level $file:$line: $value" . PHP_EOL);
            fclose($fileHandle);
        } else {
            throw new Exception('The log file could not be opened or created!');
        }
    }

}
