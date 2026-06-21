<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ForgotPasswordRequest;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Mail\Admin\AdminPasswordResetMail;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // ═══════════════════════════════════════════════
    // LOGIN
    // ═══════════════════════════════════════════════

    public function loginView()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function loginAttempt(LoginRequest $request)
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

    // ═══════════════════════════════════════════════
    // LOGOUT
    // ═══════════════════════════════════════════════

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login')
            ->with('success', 'You have been logged out successfully.');
    }

    // ═══════════════════════════════════════════════
    // FORGOT PASSWORD
    // ═══════════════════════════════════════════════

    public function showForgotPassword()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.forgot-password');
    }

    public function sendResetLink(ForgotPasswordRequest $request)
    {
        // Rate limit check
        $request->ensureIsNotRateLimited();

        $admin = Admin::where('email', $request->email)
                      ->where('is_active', true)
                      ->first();

        // Always show success message even if email not found
        // This prevents email enumeration attacks
        if (!$admin) {
            $request->incrementRateLimiter();

            return back()->with(
                'success',
                'If that email exists in our system, we have sent a reset link.'
            );
        }

        // Delete any existing token for this email
        DB::table('admin_password_reset_tokens')
          ->where('email', $admin->email)
          ->delete();

        // Generate secure token
        $token = Str::random(64);

        // Store hashed token in database
        DB::table('admin_password_reset_tokens')->insert([
            'email'      => $admin->email,
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        // Build reset URL with plain token (not hashed)
        $resetUrl = route('admin.password.reset.form', [
            'token' => $token,
            'email' => $admin->email,
        ]);

        // Send email
        Mail::to($admin->email)
            ->send(new AdminPasswordResetMail(
                resetUrl        : $resetUrl,
                adminName       : $admin->name,
                expiresInMinutes: 30,
            ));

        $request->incrementRateLimiter();

        return back()->with(
            'success',
            'If that email exists in our system, we have sent a reset link.'
        );
    }

    // ═══════════════════════════════════════════════
    // RESET PASSWORD
    // ═══════════════════════════════════════════════

    public function showResetPassword(Request $request, string $token)
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        // Basic validation before showing form
        if (!$request->has('email') || !$token) {
            return redirect()
                ->route('admin.password.request')
                ->with('error', 'Invalid password reset link.');
        }

        return view('admin.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        // Find token record
        $tokenRecord = DB::table('admin_password_reset_tokens')
                         ->where('email', $request->email)
                         ->first();

        // Validate token exists
        if (!$tokenRecord) {
            return back()->withErrors([
                'email' => 'Invalid or expired reset link. Please request a new one.',
            ]);
        }

        // Check token expiry (30 minutes)
        $createdAt = \Carbon\Carbon::parse($tokenRecord->created_at);
        if ($createdAt->addMinutes(30)->isPast()) {

            // Clean up expired token
            DB::table('admin_password_reset_tokens')
              ->where('email', $request->email)
              ->delete();

            return back()->withErrors([
                'email' => 'This reset link has expired. Please request a new one.',
            ]);
        }

        // Verify token matches hashed token
        if (!Hash::check($request->token, $tokenRecord->token)) {
            return back()->withErrors([
                'email' => 'Invalid reset link. Please request a new one.',
            ]);
        }

        // Find admin
        $admin = Admin::where('email', $request->email)
                      ->where('is_active', true)
                      ->first();

        if (!$admin) {
            return back()->withErrors([
                'email' => 'No active admin account found with this email.',
            ]);
        }

        // Update password
        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete used token
        DB::table('admin_password_reset_tokens')
          ->where('email', $request->email)
          ->delete();

        // Log out all other sessions for this admin
        Auth::guard('admin')->logoutOtherDevices($request->password);

        return redirect()
            ->route('admin.login')
            ->with('success', 'Password reset successfully. Please login with your new password.');
    }
}