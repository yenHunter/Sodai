<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AdminService
{
    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────

    public function store(array $data): Admin
    {
        return DB::transaction(function () use ($data) {

            $avatarPath = null;
            if (!empty($data['avatar'])) {
                $avatarPath = $this->uploadAvatar($data['avatar']);
            }

            $admin = Admin::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'phone'     => $data['phone'] ?? null,
                'password'  => Hash::make($data['password']),
                'avatar'    => $avatarPath,
                'is_active' => $this->resolveIsActive($data['is_active'] ?? false),
            ]);

            $role = Role::where('id', $data['role_id'])->where('guard_name', 'admin')->firstOrFail();
            $admin->assignRole($role->name);

            return $admin->fresh();
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────

    public function update(Admin $admin, array $data): Admin
    {
        return DB::transaction(function () use ($admin, $data) {

            $avatarPath = $admin->avatar;
            if (!empty($data['avatar'])) {
                $this->deleteAvatar($admin->avatar);
                $avatarPath = $this->uploadAvatar($data['avatar']);
            }

            $updatePayload = [
                'name'      => $data['name'],
                'email'     => $data['email'],
                'phone'     => $data['phone'] ?? null,
                'avatar'    => $avatarPath,
                'is_active' => $this->resolveIsActive($data['is_active'] ?? false),
            ];

            if (!empty($data['password'])) {
                $updatePayload['password'] = Hash::make($data['password']);
            }

            // Guard: cannot deactivate your own account
            if ((int) $admin->id === (int) Auth::guard('admin')->id() && !$updatePayload['is_active']) {
                throw new \Exception('You cannot deactivate your own account.');
            }

            $admin->update($updatePayload);

            $role = Role::where('id', $data['role_id'])->where('guard_name', 'admin')->firstOrFail();

            // Guard: cannot remove your own super-admin role if you're the last one
            if (
                (int) $admin->id === (int) Auth::guard('admin')->id()
                && $admin->hasRole('super-admin')
                && $role->name !== 'super-admin'
                && $this->countAdminsWithRole('super-admin') <= 1
            ) {
                throw new \Exception('You cannot remove the super-admin role from the last remaining super-admin.');
            }

            $admin->syncRoles([$role->name]);

            return $admin->fresh();
        });
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────

    public function delete(Admin $admin): bool
    {
        if ((int) $admin->id === (int) Auth::guard('admin')->id()) {
            throw new \Exception('You cannot delete your own account.');
        }

        if ($admin->hasRole('super-admin') && $this->countAdminsWithRole('super-admin') <= 1) {
            throw new \Exception('Cannot delete the last remaining super-admin.');
        }

        return DB::transaction(function () use ($admin) {
            $this->deleteAvatar($admin->avatar);
            return $admin->delete();
        });
    }

    // ─────────────────────────────────────────────
    // TOGGLE STATUS
    // ─────────────────────────────────────────────

    public function toggleStatus(Admin $admin): Admin
    {
        if ((int) $admin->id === (int) Auth::guard('admin')->id()) {
            throw new \Exception('You cannot change the status of your own account.');
        }

        $admin->update(['is_active' => !$admin->is_active]);

        return $admin->fresh();
    }

    // ─────────────────────────────────────────────
    // OWN PROFILE
    // ─────────────────────────────────────────────

    public function updateOwnProfile(Admin $admin, array $data): Admin
    {
        return DB::transaction(function () use ($admin, $data) {
            $avatarPath = $admin->avatar;

            if (!empty($data['avatar'])) {
                $this->deleteAvatar($admin->avatar);
                $avatarPath = $this->uploadAvatar($data['avatar']);
            }

            $admin->update([
                'name'   => $data['name'],
                'email'  => $data['email'],
                'phone'  => $data['phone'] ?? null,
                'avatar' => $avatarPath,
            ]);

            return $admin->fresh();
        });
    }

    public function updateOwnPassword(Admin $admin, string $newPassword): void
    {
        $admin->update(['password' => Hash::make($newPassword)]);
    }

    // ─────────────────────────────────────────────
    // IMAGE HANDLING
    // ─────────────────────────────────────────────

    private function uploadAvatar(UploadedFile $image): string
    {
        try {
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path     = $image->storeAs('admins/avatars', $filename, 'public');

            if (!$path) {
                throw new \Exception('Failed to upload avatar.');
            }

            return $path;
        } catch (\Exception $e) {
            throw new \Exception('Avatar upload failed: ' . $e->getMessage());
        }
    }

    private function deleteAvatar(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    // ─────────────────────────────────────────────
    // RESOLVE IS_ACTIVE
    // ─────────────────────────────────────────────

    private function resolveIsActive(mixed $value): bool
    {
        if (is_bool($value)) return $value;
        if (is_int($value)) return $value === 1;
        if (is_string($value)) return in_array(strtolower($value), ['active', '1', 'true']);
        return false;
    }

    // ─────────────────────────────────────────────
    // QUERY HELPERS
    // ─────────────────────────────────────────────

    public function countAdminsWithRole(string $roleName): int
    {
        return Admin::role($roleName)->count();
    }

    public function getAdminsList()
    {
        return Admin::with('roles')->latest()->get();
    }

    public function getRolesForSelect()
    {
        return Role::where('guard_name', 'admin')->orderBy('name')->get();
    }
}