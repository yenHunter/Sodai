<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Services\AdminService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Profile\UpdateProfileRequest;
use App\Http\Requests\Admin\Profile\UpdatePasswordRequest;

class ProfileController extends Controller
{
    public function __construct(
        private AdminService $adminService
    ) {}

    public function show()
    {
        $admin = Auth::guard('admin')->user()->load('roles');

        return view('admin.settings.users.profile', compact('admin'));
    }

    public function edit()
    {
        $admin = Auth::guard('admin')->user();

        return view('admin.settings.users.account-settings', compact('admin'));
    }

    public function update(UpdateProfileRequest $request)
    {
        try {
            $this->adminService->updateOwnProfile(Auth::guard('admin')->user(), $request->validated());

            return redirect()
                ->route('admin.users.profile.edit')
                ->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.users.profile.edit')
                ->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $this->adminService->updateOwnPassword(
            Auth::guard('admin')->user(),
            $request->input('password')
        );

        return redirect()
            ->route('admin.users.profile.edit')
            ->with('success', 'Password updated successfully.');
    }
}