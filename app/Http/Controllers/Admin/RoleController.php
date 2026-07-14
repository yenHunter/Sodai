<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\RoleService;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Role\StoreRoleRequest;
use App\Http\Requests\Admin\Role\UpdateRolePermissionsRequest;

class RoleController extends Controller
{
    public function __construct(
        private RoleService $roleService
    ) {}

    public function index()
    {
        $roles = $this->roleService->getRolesList();

        return view('admin.settings.users.roles', compact('roles'));
    }

    public function store(StoreRoleRequest $request)
    {
        try {
            $role = $this->roleService->store($request->validated());

            return redirect()
                ->route('admin.users.roles.edit', $role)
                ->with('success', 'Role created. Now assign its permissions.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.users.roles.index')
                ->with('error', 'Failed to create role: ' . $e->getMessage());
        }
    }

    public function edit(Role $role)
    {
        $data = $this->roleService->getRoleWithPermissions($role);

        return view('admin.settings.users.role-details', $data);
    }

    public function update(UpdateRolePermissionsRequest $request, Role $role)
    {
        try {
            $this->roleService->updatePermissions($role, $request->input('permissions', []));

            return redirect()
                ->route('admin.users.roles.index')
                ->with('success', "Permissions updated for \"{$role->name}\".");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.users.roles.edit', $role)
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Role $role)
    {
        try {
            $this->roleService->delete($role);

            return redirect()
                ->route('admin.users.roles.index')
                ->with('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.users.roles.index')
                ->with('error', $e->getMessage());
        }
    }

    public function permissions()
    {
        $groups = $this->roleService->getPermissionsGrouped();

        return view('admin.settings.users.permissions', compact('groups'));
    }
}