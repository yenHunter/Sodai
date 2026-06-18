<?php
// app/Http/Controllers/Admin/AuthController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // ─────────────────────────────────────────────
    // Show Login Form
    // ─────────────────────────────────────────────

    public function showLogin()
    {
        // Redirect to dashboard if already logged in
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    // ─────────────────────────────────────────────
    // Handle Login
    // ─────────────────────────────────────────────

    public function login(LoginRequest $request)
    {
        // Check rate limiting before attempting login
        $request->ensureIsNotRateLimited();

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (!Auth::guard('admin')->attempt($credentials, $remember)) {

            // Increment failed attempts for rate limiting
            $request->incrementRateLimiter();

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'These credentials do not match our records.',
                ]);
        }

        // ✅ Login successful

        // Clear rate limiter on success
        $request->clearRateLimiter();

        // Prevent session fixation attack
        $request->session()->regenerate();

        // Update last login info
        $admin = Auth::guard('admin')->user();
        $admin->updateLastLogin($request->ip());

        return redirect()
            ->route('admin.dashboard')
            ->with('success', "Welcome back, {$admin->name}!");
    }

    // ─────────────────────────────────────────────
    // Handle Logout
    // ─────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        // Invalidate session and regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login')
            ->with('success', 'You have been logged out successfully.');
    }
}