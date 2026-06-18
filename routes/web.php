<?php

use Illuminate\Support\Facades\Route;

// ════════════════════════════════════════════════
// ADMIN ROUTES
// ════════════════════════════════════════════════
Route::prefix('admin')->name('admin.')->group(function () {

    // ── Guest Only (redirect to dashboard if logged in) ──
    Route::middleware('guest:admin')->group(function () {

        Route::get('/login', [
            \App\Http\Controllers\Admin\AuthController::class,
            'showLogin'
        ])->name('login');

        Route::post('/login', [
            \App\Http\Controllers\Admin\AuthController::class,
            'login'
        ])->name('login.post');

    });

    // ── Authenticated Admin Routes ──
    Route::middleware(['auth.admin', 'prevent.back.history'])
         ->group(function () {

        Route::post('/logout', [
            \App\Http\Controllers\Admin\AuthController::class,
            'logout'
        ])->name('logout');

        Route::get('/dashboard', [
            \App\Http\Controllers\Admin\DashboardController::class,
            'index'
        ])->name('dashboard');

    });

});


// ════════════════════════════════════════════════
// VISITOR / CUSTOMER ROUTES (setup later)
// ════════════════════════════════════════════════
Route::get('/', function () {
    return view('welcome');
});