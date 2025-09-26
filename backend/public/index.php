<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Maintenance check
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

/** @var \Illuminate\Contracts\Http\Kernel $kernel */
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

/** @var \Illuminate\Http\Request $request */
$request = Request::capture();

$response = $kernel->handle($request);

// ✅ Log after Laravel container is booted
error_log('DEBUG FULL URL: ' . $request->fullUrl());
error_log('DEBUG PATH: ' . $request->path());

$response->send();
$kernel->terminate($request, $response);

