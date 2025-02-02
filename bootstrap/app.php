<?php

use App\Console\Commands\DatabaseBackup;
use App\Http\Middleware\IsPermissionMiddleware;
use App\Http\Middleware\UserIsActive;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            UserIsActive::class,
        ]);
        $middleware->alias([
            'isPermission' => IsPermissionMiddleware::class,
        ]);
    })
    ->withCommands([
        DatabaseBackup::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
