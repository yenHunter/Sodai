<?php

use Illuminate\Support\Facades\Route;

// ── Public Routes ──────────────────────────────────────────
Route::get('/', function () {
    return view('visitor.pages.index');
})->name('home');

// ── Auth Guest Routes (coming soon) ───────────────────────
// Route::middleware('guest:customer')->group(function () {
//     Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
//     Route::post('/login', [AuthController::class, 'login'])->name('login.post');
//     Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
//     Route::post('/register', [AuthController::class, 'register'])->name('register.post');
// });

// ── Authenticated Customer Routes (coming soon) ────────────
// Route::middleware('auth.customer')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// });

// ── Products (coming soon) ────────────────────────────────
// Route::get('/products', [ProductController::class, 'index'])->name('products.index');
// Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// ── Cart (coming soon) ────────────────────────────────────
// Route::prefix('cart')->name('cart.')->group(function () {});

// ── Checkout (coming soon) ────────────────────────────────
// Route::prefix('checkout')->name('checkout.')->group(function () {});

// ── Orders (coming soon) ──────────────────────────────────
// Route::prefix('orders')->name('orders.')->group(function () {});

// ── Wishlist (coming soon) ────────────────────────────────
// Route::prefix('wishlist')->name('wishlist.')->group(function () {});