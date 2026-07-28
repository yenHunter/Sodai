<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Visitor\AuthController;
use App\Http\Controllers\Visitor\ProductController;

// ── Product Catalog (public) ──
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/category/{category:slug}', [ProductController::class, 'byCategory'])->name('products.category');

// ── Guest Only ──
Route::middleware('guest:customer')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');

    Route::get('/forgot-password', [AuthController::class, 'forgotPasswordView'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPasswordAttempt'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'resetPasswordView'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPasswordAttempt'])->name('password.update');
});

// ── Authenticated Customer ──
Route::middleware('auth.customer')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ── Set Password (admin-created account bootstrap; token itself is the auth, no guest gate needed) ──
Route::get('/set-password/{token}', [AuthController::class, 'setPasswordView'])->name('set-password.view');
Route::post('/set-password', [AuthController::class, 'setPasswordAttempt'])->name('set-password.attempt');
