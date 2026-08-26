<?php

use App\Http\Controllers\Visitor\AccountController;
use App\Http\Controllers\Visitor\AddressController;
use App\Http\Controllers\Visitor\AuthController;
use App\Http\Controllers\Visitor\CartController;
use App\Http\Controllers\Visitor\OrderController;
use App\Http\Controllers\Visitor\ProductController;
use App\Http\Controllers\Visitor\ReviewController;
use App\Http\Controllers\Visitor\WishlistController;
use App\Http\Controllers\Visitor\HomeController;
use Illuminate\Support\Facades\Route;

// ── Landing Page ──
Route::get('/', [HomeController::class, 'index'])->name('index');

// ── Product Catalog (public) ──
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/category/{category:slug}', [ProductController::class, 'byCategory'])->name('products.category');

// ── Cart (guest or authenticated — uses session_id for guests) ──
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/{cartItem}', [CartController::class, 'update'])->name('update');
    Route::delete('/{cartItem}', [CartController::class, 'destroy'])->name('destroy');
});

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

    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'show'])->name('show');
        Route::post('/update', [AccountController::class, 'update'])->name('update');
        Route::post('/password', [AccountController::class, 'updatePassword'])->name('password');

        Route::prefix('addresses')->name('addresses.')->group(function () {
            Route::get('/', [AddressController::class, 'index'])->name('index');
            Route::post('/store', [AddressController::class, 'store'])->name('store');
            Route::post('/{address}/update', [AddressController::class, 'update'])->name('update');
            Route::delete('/{address}', [AddressController::class, 'destroy'])->name('destroy');
            Route::patch('/{address}/set-default', [AddressController::class, 'setDefault'])->name('set-default');
        });

        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        });

        Route::prefix('wishlist')->name('wishlist.')->group(function () {
            Route::get('/', [WishlistController::class, 'index'])->name('index');
            Route::post('/{product}/toggle', [WishlistController::class, 'toggle'])->name('toggle');
            Route::delete('/{product}', [WishlistController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [CartController::class, 'index'])->name('index');
            Route::post('/add', [CartController::class, 'add'])->name('add');
            Route::patch('/{cartItem}', [CartController::class, 'update'])->name('update');
            Route::delete('/{cartItem}', [CartController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/', [ReviewController::class, 'index'])->name('index');
            Route::post('/store', [ReviewController::class, 'store'])->name('store');
            Route::post('/{review}/update', [ReviewController::class, 'update'])->name('update');
            Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy');
        });
    });
});

// ── Set Password (admin-created account bootstrap; token itself is the auth, no guest gate needed) ──
Route::get('/set-password/{token}', [AuthController::class, 'setPasswordView'])->name('set-password.view');
Route::post('/set-password', [AuthController::class, 'setPasswordAttempt'])->name('set-password.attempt');
