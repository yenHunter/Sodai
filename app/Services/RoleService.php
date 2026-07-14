<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleService
{
    public const PROTECTED_ROLES = ['super-admin'];

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────

    public function store(array $data): Role
    {
        return Role::create([
            'name'       => Str::slug($data['name']),
            'guard_name' => 'admin',
        ]);
    }

    // ─────────────────────────────────────────────
    // UPDATE PERMISSIONS
    // ─────────────────────────────────────────────

    public function updatePermissions(Role $role, array $permissionIds): Role
    {
        if (in_array($role->name, self::PROTECTED_ROLES)) {
            throw new \Exception('The super-admin role has all permissions by default and cannot be modified.');
        }

        return DB::transaction(function () use ($role, $permissionIds) {
            $permissions = Permission::where('guard_name', 'admin')
                ->whereIn('id', $permissionIds)
                ->get();

            $role->syncPermissions($permissions);

            return $role->fresh('permissions');
        });
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────

    public function delete(Role $role): bool
    {
        if (in_array($role->name, self::PROTECTED_ROLES)) {
            throw new \Exception('This is a protected system role and cannot be deleted.');
        }

        if (Admin::role($role->name)->exists()) {
            throw new \Exception('Cannot delete a role that is currently assigned to one or more admins.');
        }

        return $role->delete();
    }

    // ─────────────────────────────────────────────
    // QUERY HELPERS
    // ─────────────────────────────────────────────

    public function getRolesList()
    {
        return Role::where('guard_name', 'admin')
            ->withCount('permissions')
            ->orderBy('name')
            ->get()
            ->map(function ($role) {
                $role->admins_count  = Admin::role($role->name)->count();
                $role->is_protected  = in_array($role->name, self::PROTECTED_ROLES);
                return $role;
            });
    }

    public function getRoleWithPermissions(Role $role)
    {
        $role->load('permissions');
        $assignedIds = $role->permissions->pluck('id')->toArray();

        return [
            'role'              => $role,
            'assigned_ids'      => $assignedIds,
            'permissions_by_group' => $this->getPermissionsGrouped(),
        ];
    }

    public function getPermissionsGrouped()
    {
        return Permission::where('guard_name', 'admin')
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($permission) => Str::before($permission->name, '.'));
    }
}