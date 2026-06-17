<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;

Route::middleware('guest')->group(function () {
    Route::prefix('admin')->name('admin')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->name('login.attempt');
        Route::get('register', [AuthController::class, 'register_view'])->name('register.view');
        Route::post('register', [AuthController::class, 'register'])->name('register.attempt');
        Route::get('forgot-password', [AuthController::class, 'forgot_password_view'])->name('forgot.password.view');
        Route::post('forgot-password', [AuthController::class, 'forgot_password'])->name('forgot.password.attempt');
        Route::get('reset-password/{token}', [AuthController::class, 'reset_password_view'])->name('password.reset');
        Route::post('reset-password', [AuthController::class, 'reset_password'])->name('reset.password.attempt');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('admin', function () {
        return view('admin.pages.index');
    });
});
