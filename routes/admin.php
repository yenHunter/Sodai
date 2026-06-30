<?php

use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;

// Guest
Route::middleware('guest:admin')->group(function () {
    Route::get('login', [AuthController::class, 'loginView'])->name('login.view');
    Route::post('login', [AuthController::class, 'loginAttempt'])->name('login.attempt');
    Route::get('forgot-password', [AuthController::class, 'forgotPasswordView'])->name('forgot-password.view');
    Route::post('forgot-password', [AuthController::class, 'forgotPasswordAttempt'])->name('forgot-password.attempt');
    Route::get('reset-password/{token}', [AuthController::class, 'resetPasswordView'])->name('reset-password.view');
    Route::post('reset-password', [AuthController::class, 'resetPasswordAttempt'])->name('reset-password.attempt');
});

// Authenticated
Route::middleware(['auth.admin', 'prevent.back.history'])->group(function () {

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::name('ecommerce.')->group(function () {
        // ── Categories: only roles with category permissions ──
        Route::middleware('permission:category.view')->group(function () {
            Route::resource('category', CategoryController::class);
        });

        // ── Products: only roles with product permissions ──
        Route::middleware('permission:product.view')->group(function () {
            // Route::resource('products', ProductController::class);
        });

        // ── Orders ──
        Route::middleware('permission:order.view')->group(function () {
            // Route::resource('orders', OrderController::class);
        });
    });

    // ── Admin Management: super-admin only ──
    Route::middleware('permission:admin.view')->group(function () {
        // Route::resource('admins', AdminController::class);
    });

    // ── Settings: super-admin only ──
    Route::middleware('permission:setting.view')->group(function () {
        // Route::get('settings', ...)->name('settings.index');
    });
});
