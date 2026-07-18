<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CategoryController;
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
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::post('/store', [CategoryController::class, 'store'])->name('store')->middleware('permission:category.create');
            Route::post('/{category}/update', [CategoryController::class, 'update'])->name('update')->middleware('permission:category.edit');
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy')->middleware('permission:category.delete');
            Route::delete('/', [CategoryController::class, 'bulkDestroy'])->name('bulk-destroy')->middleware('permission:category.delete');
            Route::patch('/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status')->middleware('permission:category.edit');
        });

        // ── Brands: only roles with brand permissions ──
        Route::middleware('permission:brand.view')->prefix('brands')->name('brand.')->group(function () {
            Route::get('/', [BrandController::class, 'index'])->name('index');
            Route::post('/store', [BrandController::class, 'store'])->name('store')->middleware('permission:brand.create');
            Route::post('/{brand}/update', [BrandController::class, 'update'])->name('update')->middleware('permission:brand.edit');
            Route::delete('/{brand}', [BrandController::class, 'destroy'])->name('destroy')->middleware('permission:brand.delete');
            Route::delete('/', [BrandController::class, 'bulkDestroy'])->name('bulk-destroy')->middleware('permission:brand.delete');
            Route::patch('/{brand}/toggle-status', [BrandController::class, 'toggleStatus'])->name('toggle-status')->middleware('permission:brand.edit');
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
        Route::middleware('permission:order.view')->prefix('orders')->name('order.')->group(function () {
            Route::get('/',       [OrderController::class, 'index'])->name('index');
            Route::get('/create', [OrderController::class, 'create'])->name('create')->middleware('permission:order.create');
            Route::post('/store', [OrderController::class, 'store'])->name('store')->middleware('permission:order.create');

            // POS AJAX endpoints — must be BEFORE the {order} wildcard
            Route::get('/customers/search',            [OrderController::class, 'searchCustomers'])->name('customers.search');
            Route::post('/customers/quick-create',      [OrderController::class, 'quickCreateCustomer'])->name('customers.quick-create')->middleware('permission:order.create');
            Route::get('/customers/{customer}/address', [OrderController::class, 'getCustomerAddress'])->name('customers.address');
            Route::get('/products/search',              [OrderController::class, 'searchProducts'])->name('products.search');

            Route::get('/{order}',        [OrderController::class, 'show'])->name('show');
            Route::get('/{order}/edit',    [OrderController::class, 'edit'])->name('edit')->middleware('permission:order.edit');
            Route::post('/{order}/update', [OrderController::class, 'update'])->name('update')->middleware('permission:order.edit');
            Route::delete('/{order}',      [OrderController::class, 'destroy'])->name('destroy')->middleware('permission:order.delete');

            Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('status.update')->middleware('permission:order.update-status');
            Route::patch('/{order}/cancel',  [OrderController::class, 'cancel'])->name('cancel')->middleware('permission:order.cancel');
        });

        // ── Customers ──
        Route::middleware('permission:customer.view')->prefix('customers')->name('customer.')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('index');
            Route::post('/store', [CustomerController::class, 'store'])->name('store')->middleware('permission:customer.create');
            Route::post('/{customer}/update', [CustomerController::class, 'update'])->name('update')->middleware('permission:customer.edit');
            Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy')->middleware('permission:customer.delete');
            Route::delete('/', [CustomerController::class, 'bulkDestroy'])->name('bulk-destroy')->middleware('permission:customer.delete');
            Route::patch('/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('toggle-status')->middleware('permission:customer.edit');
            Route::post('/{customer}/resend-set-password', [CustomerController::class, 'resendSetPassword'])->name('resend-set-password')->middleware('permission:customer.edit');
        });
    });

    // ── Admin Management: super-admin only ──
    Route::prefix('users')->name('users.')->group(function () {
        Route::middleware('permission:admin.view')->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('index');
            Route::post('/store', [AdminController::class, 'store'])->name('store')->middleware('permission:admin.create');
            Route::post('/{admin}/update', [AdminController::class, 'update'])->name('update')->middleware('permission:admin.edit');
            Route::delete('/{admin}', [AdminController::class, 'destroy'])->name('destroy')->middleware('permission:admin.delete');
            Route::patch('/{admin}/toggle-status', [AdminController::class, 'toggleStatus'])->name('toggle-status')->middleware('permission:admin.edit');
        });

        // ── Role & Permission Management: super-admin only ──
        Route::middleware('permission:role.view')->group(function () {
            Route::prefix('roles')->name('roles.')->group(function () {
                Route::get('/', [RoleController::class, 'index'])->name('index');
                Route::post('/store', [RoleController::class, 'store'])->name('store')->middleware('permission:role.create');
                Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit')->middleware('permission:role.edit');
                Route::post('/{role}/update', [RoleController::class, 'update'])->name('update')->middleware('permission:role.edit');
                Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy')->middleware('permission:role.delete');
            });

            Route::get('/permissions', [RoleController::class, 'permissions'])->name('permissions.index');
        });

        // ── Own Profile: any authenticated admin ──
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'show'])->name('show');
            Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
            Route::post('/update', [ProfileController::class, 'update'])->name('update');
            Route::post('/password', [ProfileController::class, 'updatePassword'])->name('password');
        });
    });
});
