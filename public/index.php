#!/usr/bin/env php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Check if the application is in maintenance mode
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader
require __DIR__.'/../vendor/autoload.php';

// Bootstrap the Laravel application and handle the request
$app = require_once __DIR__.'/../bootstrap/app.php';

// Handle the incoming HTTP request
$app->handle(Request::capture());
