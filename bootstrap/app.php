<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// Arahkan folder storage ke /tmp jika berada di server read-only (Wasmer)
if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production') {
    if (!is_dir('/tmp/storage/framework/views')) {
        @mkdir('/tmp/storage/framework/views', 0777, true);
        @mkdir('/tmp/storage/framework/cache', 0777, true);
        @mkdir('/tmp/storage/framework/sessions', 0777, true);
        @mkdir('/tmp/storage/logs', 0777, true);
    }
    $app->useStoragePath('/tmp/storage');
}

return $app;