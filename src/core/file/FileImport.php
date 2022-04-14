<?php
namespace core\file;

use RuntimeException;

class FileImport {

    static function onChange(string $path, callable $cb) {
        $modified = @filemtime($path);
        if (!$modified) throw new RuntimeException("$path not found");
        $imported = @filemtime("$path.imported");
        if ($imported === false || $imported !== $modified) {
            $cb($path);
            touch("$path.imported", $modified);
        }
    }

}
