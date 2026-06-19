<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.pages.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $request->ensureIsNotRateLimited();

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (!Auth::guard('admin')->attempt($credentials, $remember)) {
            $request->incrementRateLimiter();

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'These credentials do not match our records.',
                ]);
        }

        $request->clearRateLimiter();
        $request->session()->regenerate();

        $admin = Auth::guard('admin')->user();
        $admin->updateLastLogin($request->ip());

        return redirect()
            ->route('admin.dashboard')
            ->with('success', "Welcome back, {$admin->name}!");
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login')
            ->with('success', 'You have been logged out successfully.');
    }
}