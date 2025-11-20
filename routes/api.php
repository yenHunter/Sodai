<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// V1 API Routes
Route::prefix('v1')->group(function () {

    // Public Auth Routes
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
    });

    // Protected Routes
    Route::middleware('auth:api')->group(function () {

        // Auth Management
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('me', [AuthController::class, 'me']);
        });

        // Test Role Middleware (Example)
        Route::get('admin-only', function () {
            return response()->json(['message' => 'Welcome Admin']);
        })->middleware('role:admin');
    });

    Route::get('products', [App\Http\Controllers\Api\ProductController::class, 'index']);

    Route::middleware('auth:api')->group(function () {

        // Vendor Routes
        Route::middleware('role:vendor,admin')->group(function () {
            Route::post('products', [App\Http\Controllers\Api\ProductController::class, 'store']);
            Route::post('products/import', [App\Http\Controllers\Api\ProductController::class, 'import']);
        });

        // Customer Routes
        Route::post('orders', [App\Http\Controllers\Api\OrderController::class, 'store']);
        Route::get('my-orders', [App\Http\Controllers\Api\OrderController::class, 'index']);

        Route::patch('orders/{order}/cancel', [App\Http\Controllers\Api\OrderController::class, 'cancel']);
        Route::patch('orders/{order}/status', [App\Http\Controllers\Api\OrderController::class, 'updateStatus']);
    });
});
