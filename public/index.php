<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Polyfills for hosts that disable certain PHP functions
if (! function_exists('ignore_user_abort')) {
    function ignore_user_abort(bool $enable = false): int { return 0; }
}
if (! function_exists('tmpfile')) {
    function tmpfile() {
        $path = tempnam(sys_get_temp_dir(), 'php');
        return fopen($path, 'r+');
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
