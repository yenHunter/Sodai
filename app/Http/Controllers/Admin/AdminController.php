<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Services\Admin\AdminService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreAdminRequest;
use App\Http\Requests\Admin\User\UpdateAdminRequest;

class AdminController extends Controller
{
    public function __construct(
        private AdminService $adminService
    ) {}

    public function index()
    {
        $admins = $this->adminService->getAdminsList();
        $roles  = $this->adminService->getRolesForSelect();

        return view('admin.settings.users.index', compact('admins', 'roles'));
    }

    public function store(StoreAdminRequest $request)
    {
        try {
            $this->adminService->store($request->validated());

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Admin created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Failed to create admin: ' . $e->getMessage());
        }
    }

    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        try {
            $this->adminService->update($admin, $request->validated());

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Admin updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Failed to update admin: ' . $e->getMessage());
        }
    }

    public function destroy(Admin $admin)
    {
        try {
            $this->adminService->delete($admin);

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Admin deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', $e->getMessage());
        }
    }

    public function toggleStatus(Admin $admin)
    {
        try {
            $updated = $this->adminService->toggleStatus($admin);
            $status  = $updated->is_active ? 'activated' : 'deactivated';

            return redirect()
                ->route('admin.users.index')
                ->with('success', "Admin {$status} successfully.");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', $e->getMessage());
        }
    }
}