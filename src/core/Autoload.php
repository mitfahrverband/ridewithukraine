<?php
use core\Config;
use core\Log;

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/..');
spl_autoload_register(function ($class) {
    $name = str_replace('\\', '/', $class) . '.php';
    if (!@include($name)) {
        if ($class === 'AppConfig') return;
        $error = error_get_last();
        Log::log($error['type'], $error['message']);
    }
});

set_exception_handler(function ($e) {
    ob_clean();
    Log::log(E_ERROR, $e);
    $errorPage = Config::get('server', 'errorPage', Config::$documentRoot . '/error.html');
    @include $errorPage;
    die;
});

Config::load();

spl_autoload_call('AppConfig');
?>
