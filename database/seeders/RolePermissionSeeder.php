<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();

        // ═══════════════════════════════════════════
        // DEFINE ALL PERMISSIONS
        // ═══════════════════════════════════════════

        $permissions = [

            // Dashboard
            'dashboard.view',

            // Categories
            'category.view',
            'category.create',
            'category.edit',
            'category.delete',

            // Products
            'product.view',
            'product.create',
            'product.edit',
            'product.delete',

            // Orders
            'order.view',
            'order.update-status',
            'order.cancel',
            'order.delete',

            // Customers
            'customer.view',
            'customer.ban',
            'customer.delete',

            // Coupons
            'coupon.view',
            'coupon.create',
            'coupon.edit',
            'coupon.delete',

            // Banners
            'banner.view',
            'banner.create',
            'banner.edit',
            'banner.delete',

            // Reviews
            'review.view',
            'review.approve',
            'review.reject',
            'review.delete',

            // Reports
            'report.view',

            // Settings
            'setting.view',
            'setting.edit',

            // Admin Management
            'admin.view',
            'admin.create',
            'admin.edit',
            'admin.delete',
        ];

        // Create all permissions for admin guard
        foreach ($permissions as $permission) {
            Permission::create([
                'name'       => $permission,
                'guard_name' => 'admin',
            ]);
        }

        // ═══════════════════════════════════════════
        // CREATE ROLES & ASSIGN PERMISSIONS
        // ═══════════════════════════════════════════

        // ── Super Admin: All permissions ────────────
        $superAdmin = Role::create([
            'name'       => 'super-admin',
            'guard_name' => 'admin',
        ]);
        $superAdmin->givePermissionTo(Permission::where('guard_name', 'admin')->get());


        // ── Manager: Everything except admin management ──
        $manager = Role::create([
            'name'       => 'manager',
            'guard_name' => 'admin',
        ]);
        $manager->givePermissionTo([
            'dashboard.view',

            'category.view',
            'category.create',
            'category.edit',
            'category.delete',

            'product.view',
            'product.create',
            'product.edit',
            'product.delete',

            'order.view',
            'order.update-status',
            'order.cancel',
            'order.delete',

            'customer.view',
            'customer.ban',

            'coupon.view',
            'coupon.create',
            'coupon.edit',
            'coupon.delete',

            'banner.view',
            'banner.create',
            'banner.edit',
            'banner.delete',

            'review.view',
            'review.approve',
            'review.reject',
            'review.delete',

            'report.view',
            'setting.view',
        ]);


        // ── Order Manager: Orders & customers only ──
        $orderManager = Role::create([
            'name'       => 'order-manager',
            'guard_name' => 'admin',
        ]);
        $orderManager->givePermissionTo([
            'dashboard.view',

            'order.view',
            'order.update-status',
            'order.cancel',

            'customer.view',
        ]);


        // ── Content Editor: Products, Categories, Banners ──
        $contentEditor = Role::create([
            'name'       => 'content-editor',
            'guard_name' => 'admin',
        ]);
        $contentEditor->givePermissionTo([
            'dashboard.view',

            'category.view',
            'category.create',
            'category.edit',

            'product.view',
            'product.create',
            'product.edit',

            'banner.view',
            'banner.create',
            'banner.edit',
        ]);


        // ── Support: View orders, handle reviews & customers ──
        $support = Role::create([
            'name'       => 'support',
            'guard_name' => 'admin',
        ]);
        $support->givePermissionTo([
            'dashboard.view',

            'order.view',

            'customer.view',

            'review.view',
            'review.approve',
            'review.reject',
        ]);
    }
}