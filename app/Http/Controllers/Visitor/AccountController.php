<?php

namespace App\Http\Controllers\Visitor;

use Illuminate\Support\Facades\Auth;
use App\Services\Visitor\AccountService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Visitor\Account\UpdateAccountRequest;
use App\Http\Requests\Visitor\Account\UpdatePasswordRequest;

class AccountController extends Controller
{
    public function __construct(
        private AccountService $accountService
    ) {}

    public function show()
    {
        $customer = Auth::guard('customer')->user();
        $addresses = $customer->addresses()->orderByDesc('is_default')->get();

        return view('visitor.pages.user-profile', compact('customer', 'addresses'));
    }

    public function update(UpdateAccountRequest $request)
    {
        try {
            $customer = $this->accountService->updateProfile(
                Auth::guard('customer')->user(),
                $request->validated()
            );

            return redirect()->back()->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $this->accountService->updatePassword(
            Auth::guard('customer')->user(),
            $request->input('password')
        );

        return redirect()->back()->with('success', 'Password updated successfully.');
    }
}