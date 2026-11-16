<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Set variabel environment langsung untuk server Wasmer
$wasmerEnv = [
    'APP_NAME'       => 'Tirta Bima',
    'APP_ENV'        => 'production',
    'APP_DEBUG'      => 'true',
    'APP_KEY'        => 'base64:u9v8w7x6y5z4a3b2c1d0e9f8g7h6i5j4k3l2m1n0o9p=',
    'APP_URL'        => 'https://tirta-bima-app.wasmer.app',
    'DB_CONNECTION'  => 'mysql',
    'DB_HOST'        => 'db.fr-roub1.bengt.wasmernet.com',
    'DB_PORT'        => '20184',
    'DB_DATABASE'    => 'tirta_bima_db',
    'DB_USERNAME'    => 'user_87e785c3',
    'DB_PASSWORD'    => 'pw_CaTIscLNqIydAur29ZDczB0eINGCxP18',
    'LOG_CHANNEL'    => 'stderr',
    'SESSION_DRIVER' => 'cookie',
    'CACHE_STORE'    => 'array',
];

foreach ($wasmerEnv as $key => $value) {
    if (!getenv($key)) {
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

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

// Arahkan storage ke /tmp agar tidak terblokir read-only filesystem Wasmer
if (!is_dir('/tmp/storage/framework/views')) {
    @mkdir('/tmp/storage/framework/views', 0777, true);
    @mkdir('/tmp/storage/framework/cache', 0777, true);
    @mkdir('/tmp/storage/framework/sessions', 0777, true);
    @mkdir('/tmp/storage/logs', 0777, true);
}
$app->useStoragePath('/tmp/storage');

return $app;