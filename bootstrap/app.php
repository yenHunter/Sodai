<?php

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

        // Global Middleware
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Named Middleware (Aliases)
        $middleware->alias([
            'auth.admin'           => \App\Http\Middleware\AdminAuthenticated::class,
            'auth.customer'        => \App\Http\Middleware\CustomerAuthenticated::class,
            'prevent.back.history' => \App\Http\Middleware\PreventBackHistory::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();