<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use Tests\Traits\AdminTestHelpers;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminUserModuleTest extends TestCase
{
    use RefreshDatabase, AdminTestHelpers;

    public function test_admin_with_permission_can_create_new_admin(): void
    {
        $admin = $this->createAdminWithPermissions(['admin.view', 'admin.create']);
        $role  = Role::create(['name' => 'staff', 'guard_name' => 'admin']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.users.store'), [
                'name'                  => 'New Staff',
                'email'                 => 'staff@example.com',
                'password'              => 'Password123',
                'password_confirmation' => 'Password123',
                'role_id'               => $role->id,
                'is_active'             => 'active',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('admins', ['email' => 'staff@example.com']);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = $this->createAdminWithPermissions(['admin.view', 'admin.delete']);

        $this->actingAsAdmin($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('admins', ['id' => $admin->id, 'deleted_at' => null]);
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $admin = $this->createAdminWithPermissions(['admin.view', 'admin.edit']);
        $role  = Role::create(['name' => 'self-role', 'guard_name' => 'admin']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.users.update', $admin), [
                'name'      => $admin->name,
                'email'     => $admin->email,
                'role_id'   => $role->id,
                'is_active' => 'inactive',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('admins', ['id' => $admin->id, 'is_active' => true]);
    }

    public function test_last_super_admin_cannot_be_deleted(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $otherAdmin = $this->createAdminWithPermissions(['admin.view', 'admin.delete']);

        $this->actingAsAdmin($otherAdmin)
            ->delete(route('admin.users.destroy', $superAdmin))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('admins', ['id' => $superAdmin->id, 'deleted_at' => null]);
    }

    public function test_super_admin_role_permissions_cannot_be_edited(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $role       = Role::where('name', 'super-admin')->where('guard_name', 'admin')->first();

        $this->actingAsAdmin($superAdmin)
            ->post(route('admin.users.roles.update', $role), ['permissions' => []])
            ->assertRedirect(route('admin.users.roles.edit', $role));
    }

    public function test_role_assigned_to_admins_cannot_be_deleted(): void
    {
        $admin = $this->createSuperAdmin();
        $role  = Role::create(['name' => 'in-use-role', 'guard_name' => 'admin']);
        Admin::factory()->create()->assignRole($role);

        $this->actingAsAdmin($admin)
            ->delete(route('admin.users.roles.destroy', $role))
            ->assertRedirect(route('admin.users.roles.index'));

        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    public function test_any_authenticated_admin_can_view_own_profile(): void
    {
        $admin = $this->createAdminWithPermissions([]); // no special permissions

        $this->actingAsAdmin($admin)
            ->get(route('admin.users.profile.show'))
            ->assertOk();
    }

    public function test_admin_can_update_own_profile(): void
    {
        $admin = $this->createAdminWithPermissions([]);

        $response = $this->actingAsAdmin($admin)
            ->post(route('admin.users.profile.update'), [
                'name'  => 'Updated Name',
                'email' => $admin->email,
            ]);

        dump($response->status(), $response->headers->get('Location'));

        $response->assertRedirect(route('admin.users.profile.edit'));
    }

    public function test_admin_cannot_change_password_with_wrong_current_password(): void
    {
        $admin = $this->createAdminWithPermissions([]);

        $this->actingAsAdmin($admin)
            ->post(route('admin.users.profile.password'), [
                'current_password'     => 'wrong-password',
                'password'              => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ])
            ->assertSessionHasErrors('current_password');
    }

    public function test_admin_can_change_password_with_correct_current_password(): void
    {
        $admin = Admin::factory()->create(['password' => bcrypt('OldPassword123')]);
        $role  = Role::create(['name' => 'basic', 'guard_name' => 'admin']);
        $admin->assignRole($role);

        $this->actingAsAdmin($admin)
            ->post(route('admin.users.profile.password'), [
                'current_password'     => 'OldPassword123',
                'password'              => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ])
            ->assertRedirect(route('admin.users.profile.edit'));

        $this->assertTrue(Hash::check('NewPassword123!', $admin->fresh()->password));
    }
}
