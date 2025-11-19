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
        Route::get('admin-only', function() {
            return response()->json(['message' => 'Welcome Admin']);
        })->middleware('role:admin');

    });
});
