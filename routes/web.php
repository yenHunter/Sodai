<?php

use Illuminate\Support\Facades\Route;

// ============================================
// VISITOR / CUSTOMER ROUTES
// ============================================
Route::prefix('/')->name('visitor.')->group(function () {

    // Public Routes
    Route::get('/', [
        \App\Http\Controllers\Visitor\HomeController::class, 'index'
    ])->name('home');

    Route::get('/products', [
        \App\Http\Controllers\Visitor\ProductController::class, 'index'
    ])->name('products.index');

    Route::get('/products/{slug}', [
        \App\Http\Controllers\Visitor\ProductController::class, 'show'
    ])->name('products.show');

    // Auth Routes (Guest Only)
    Route::middleware('guest:customer')->group(function () {
        Route::get('/login', [
            \App\Http\Controllers\Visitor\AuthController::class, 'showLogin'
        ])->name('login');

        Route::post('/login', [
            \App\Http\Controllers\Visitor\AuthController::class, 'login'
        ])->name('login.post');

        Route::get('/register', [
            \App\Http\Controllers\Visitor\AuthController::class, 'showRegister'
        ])->name('register');

        Route::post('/register', [
            \App\Http\Controllers\Visitor\AuthController::class, 'register'
        ])->name('register.post');

        Route::get('/forgot-password', [
            \App\Http\Controllers\Visitor\AuthController::class, 'showForgotPassword'
        ])->name('password.request');

        Route::post('/forgot-password', [
            \App\Http\Controllers\Visitor\AuthController::class, 'sendResetLink'
        ])->name('password.email');
    });

    // Cart (works for guest too)
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [
            \App\Http\Controllers\Visitor\CartController::class, 'index'
        ])->name('index');
        Route::post('/add', [
            \App\Http\Controllers\Visitor\CartController::class, 'add'
        ])->name('add');
        Route::patch('/update/{cartItem}', [
            \App\Http\Controllers\Visitor\CartController::class, 'update'
        ])->name('update');
        Route::delete('/remove/{cartItem}', [
            \App\Http\Controllers\Visitor\CartController::class, 'remove'
        ])->name('remove');
        Route::post('/coupon', [
            \App\Http\Controllers\Visitor\CartController::class, 'applyCoupon'
        ])->name('coupon.apply');
    });

    // Authenticated Customer Routes
    Route::middleware('auth.customer')->group(function () {

        Route::post('/logout', [
            \App\Http\Controllers\Visitor\AuthController::class, 'logout'
        ])->name('logout');

        // Profile
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [
                \App\Http\Controllers\Visitor\ProfileController::class, 'index'
            ])->name('index');
            Route::put('/', [
                \App\Http\Controllers\Visitor\ProfileController::class, 'update'
            ])->name('update');
            Route::put('/password', [
                \App\Http\Controllers\Visitor\ProfileController::class, 'updatePassword'
            ])->name('password.update');
        });

        // Checkout
        Route::prefix('checkout')->name('checkout.')->group(function () {
            Route::get('/', [
                \App\Http\Controllers\Visitor\CheckoutController::class, 'index'
            ])->name('index');
            Route::post('/place-order', [
                \App\Http\Controllers\Visitor\CheckoutController::class, 'placeOrder'
            ])->name('place-order');
        });

        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [
                \App\Http\Controllers\Visitor\OrderController::class, 'index'
            ])->name('index');
            Route::get('/{orderNumber}', [
                \App\Http\Controllers\Visitor\OrderController::class, 'show'
            ])->name('show');
            Route::post('/{order}/cancel', [
                \App\Http\Controllers\Visitor\OrderController::class, 'cancel'
            ])->name('cancel');
        });

        // Wishlist
        Route::prefix('wishlist')->name('wishlist.')->group(function () {
            Route::get('/', [
                \App\Http\Controllers\Visitor\WishlistController::class, 'index'
            ])->name('index');
            Route::post('/toggle/{product}', [
                \App\Http\Controllers\Visitor\WishlistController::class, 'toggle'
            ])->name('toggle');
        });

        // Reviews
        Route::post('/reviews', [
            \App\Http\Controllers\Visitor\ReviewController::class, 'store'
        ])->name('reviews.store');
    });
});


// ============================================
// ADMIN ROUTES
// ============================================
Route::prefix('admin')->name('admin.')->group(function () {

    // Admin Guest Routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [
            \App\Http\Controllers\Admin\AuthController::class, 'showLogin'
        ])->name('login');

        Route::post('/login', [
            \App\Http\Controllers\Admin\AuthController::class, 'login'
        ])->name('login.post');
    });

    // Admin Authenticated Routes
    Route::middleware(['auth.admin', 'prevent.back.history'])->group(function () {

        Route::post('/logout', [
            \App\Http\Controllers\Admin\AuthController::class, 'logout'
        ])->name('logout');

        // Dashboard
        Route::get('/dashboard', [
            \App\Http\Controllers\Admin\DashboardController::class, 'index'
        ])->name('dashboard');

        // Categories
        Route::resource('categories', 
            \App\Http\Controllers\Admin\CategoryController::class
        );

        // Products
        Route::resource('products', 
            \App\Http\Controllers\Admin\ProductController::class
        );

        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [
                \App\Http\Controllers\Admin\OrderController::class, 'index'
            ])->name('index');
            Route::get('/{order}', [
                \App\Http\Controllers\Admin\OrderController::class, 'show'
            ])->name('show');
            Route::patch('/{order}/status', [
                \App\Http\Controllers\Admin\OrderController::class, 'updateStatus'
            ])->name('status.update');
        });

        // Customers
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [
                \App\Http\Controllers\Admin\CustomerController::class, 'index'
            ])->name('index');
            Route::get('/{user}', [
                \App\Http\Controllers\Admin\CustomerController::class, 'show'
            ])->name('show');
            Route::patch('/{user}/ban', [
                \App\Http\Controllers\Admin\CustomerController::class, 'ban'
            ])->name('ban');
        });

        // Coupons
        Route::resource('coupons', 
            \App\Http\Controllers\Admin\CouponController::class
        );

        // Banners
        Route::resource('banners', 
            \App\Http\Controllers\Admin\BannerController::class
        );

        // Reviews
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/', [
                \App\Http\Controllers\Admin\ReviewController::class, 'index'
            ])->name('index');
            Route::patch('/{review}/approve', [
                \App\Http\Controllers\Admin\ReviewController::class, 'approve'
            ])->name('approve');
            Route::patch('/{review}/reject', [
                \App\Http\Controllers\Admin\ReviewController::class, 'reject'
            ])->name('reject');
        });

        // Settings
        Route::get('/settings', [
            \App\Http\Controllers\Admin\SettingController::class, 'index'
        ])->name('settings.index');
        Route::put('/settings', [
            \App\Http\Controllers\Admin\SettingController::class, 'update'
        ])->name('settings.update');

        // Reports
        Route::get('/reports/sales', [
            \App\Http\Controllers\Admin\ReportController::class, 'sales'
        ])->name('reports.sales');
    });
});