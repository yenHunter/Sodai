<?php

namespace Tests\Traits;

use App\Models\Admin;
use Database\Seeders\RolePermissionSeeder;
use Spatie\Permission\Models\Role;

trait AdminTestHelpers
{
    protected bool $permissionsSeededForTest = false;

    protected function ensurePermissionsSeeded(): void
    {
        if (! $this->permissionsSeededForTest) {
            $this->seed(RolePermissionSeeder::class);
            $this->permissionsSeededForTest = true;
        }
    }

    protected function createAdminWithPermissions(array $permissions = [], string $roleName = 'test-role'): Admin
    {
        $this->ensurePermissionsSeeded();

        $admin = Admin::factory()->create(['is_active' => true]);

        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'admin']);
        $role->syncPermissions($permissions);
        $admin->assignRole($role);

        return $admin;
    }

    protected function createSuperAdmin(): Admin
    {
        $this->ensurePermissionsSeeded();

        $admin = Admin::factory()->create(['is_active' => true]);
        $role = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'admin']);
        $admin->assignRole($role);

        return $admin;
    }

    protected function actingAsAdmin(Admin $admin)
    {
        return $this->actingAs($admin, 'admin');
    }
}
