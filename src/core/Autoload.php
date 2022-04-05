<?php
use core\Config;

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/..');
spl_autoload_register(function ($class) {
    $name = str_replace('\\', '/', $class) . '.php';
    if (!@include($name)) {
        if ($class === 'AppConfig') return;
        $error = error_get_last();
        error_log($error['message']);
    }
});

Config::load();
spl_autoload_call('AppConfig');
?>
