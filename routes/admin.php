<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
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

    Route::prefix('ecommerce')->name('ecommerce.')->group(function () {
        // ── Categories: only roles with category permissions ──
        Route::middleware('permission:category.view')->prefix('categories')->name('category.')->group(function () {

            Route::get('/', [CategoryController::class, 'index'])
                ->name('index');

            Route::post('/store', [CategoryController::class, 'store'])
                ->name('store')
                ->middleware('permission:category.create');

            Route::post('/{category}/update', [CategoryController::class, 'update'])
                ->name('update')
                ->middleware('permission:category.edit');

            Route::delete('/{category}', [CategoryController::class, 'destroy'])
                ->name('destroy')
                ->middleware('permission:category.delete');

            Route::delete('/', [CategoryController::class, 'bulkDestroy'])
                ->name('bulk-destroy')
                ->middleware('permission:category.delete');

            Route::patch('/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])
                ->name('toggle-status')
                ->middleware('permission:category.edit');
        });

        // — Products: only roles with product permissions —
        Route::middleware('permission:product.view')->prefix('products')->name('product.')->group(function () {

            Route::get('/',        [ProductController::class, 'index'])->name('index');
            Route::get('/create',  [ProductController::class, 'create'])->name('create');
            Route::post('/store',  [ProductController::class, 'store'])->name('store')->middleware('permission:product.create');

            // Bulk destroy BEFORE /{product} wildcard — critical ordering
            Route::delete('/bulk-destroy', [ProductController::class, 'bulkDestroy'])->name('bulk-destroy')->middleware('permission:product.delete');

            // Tag search BEFORE /{product} wildcard
            Route::get('/tags/search', [ProductController::class, 'searchTags'])->name('tags.search');

            // Product search for Select2 BEFORE /{product} wildcard
            Route::get('/search', [ProductController::class, 'search'])->name('search');

            // Wildcard routes AFTER all static routes
            Route::get('/{product}',      [ProductController::class, 'show'])->name('show');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit')->middleware('permission:product.edit');
            Route::post('/{product}/update', [ProductController::class, 'update'])->name('update')->middleware('permission:product.edit');
            Route::delete('/{product}',   [ProductController::class, 'destroy'])->name('destroy')->middleware('permission:product.delete');

            Route::patch('/{product}/toggle-status',   [ProductController::class, 'toggleStatus'])->name('toggle-status')->middleware('permission:product.edit');
            Route::patch('/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('toggle-featured')->middleware('permission:product.edit');

            // Image management AJAX endpoints
            Route::delete('/{product}/images/{image}',         [ProductController::class, 'deleteImage'])->name('images.delete')->middleware('permission:product.edit');
            Route::patch('/{product}/images/{image}/primary',  [ProductController::class, 'setPrimaryImage'])->name('images.set-primary')->middleware('permission:product.edit');
            Route::post('/{product}/images/reorder',           [ProductController::class, 'reorderImages'])->name('images.reorder')->middleware('permission:product.edit');

            // Stock quick update
            Route::patch('/{product}/stock', [ProductController::class, 'updateStock'])->name('stock.update')->middleware('permission:product.edit');
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
