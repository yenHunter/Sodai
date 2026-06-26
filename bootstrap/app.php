<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Global middleware - applies to all requests
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Named middleware aliases
        $middleware->alias([
            'guest'                => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'auth.admin'           => \App\Http\Middleware\AdminAuthenticated::class,
            'auth.customer'        => \App\Http\Middleware\CustomerAuthenticated::class,
            'prevent.back.history' => \App\Http\Middleware\PreventBackHistory::class,
            'permission'           => \App\Http\Middleware\CheckAdminPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
