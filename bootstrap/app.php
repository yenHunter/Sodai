<?php

use App\Http\Middleware\AdminAuthenticated;
use App\Http\Middleware\CheckAdminPermission;
use App\Http\Middleware\CustomerAuthenticated;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->name('visitor.')
                ->group(base_path('routes/visitor.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Global middleware - applies to all requests
        $middleware->append(SecurityHeaders::class);

        // Named middleware aliases
        $middleware->alias([
            'guest' => RedirectIfAuthenticated::class,
            'auth.admin' => AdminAuthenticated::class,
            'auth.customer' => CustomerAuthenticated::class,
            'prevent.back.history' => PreventBackHistory::class,
            'permission' => CheckAdminPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
