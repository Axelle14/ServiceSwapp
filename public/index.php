<?php
declare(strict_types=1);


// On InfinityFree (and most shared hosts), FTP root IS htdocs.
// Everything lives inside htdocs/, so APP_ROOT = this file's directory.
define('APP_ROOT', dirname(__DIR__));

// Autoloader
spl_autoload_register(function (string $class): void {
    $file = APP_ROOT . '/app/' . str_replace(['App\\', '\\'], ['', '/'], $class) . '.php';
    if (file_exists($file)) require_once $file;
});

require_once APP_ROOT . '/config/bootstrap.php';
require_once APP_ROOT . '/config/routes.php';
