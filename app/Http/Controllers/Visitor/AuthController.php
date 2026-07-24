<?php

namespace App\Http\Controllers\Visitor;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use App\Mail\Customer\CustomerPasswordResetMail;
use App\Http\Requests\Visitor\Auth\LoginRequest;
use App\Http\Requests\Visitor\Auth\RegisterRequest;
use App\Http\Requests\Visitor\Auth\ForgotPasswordRequest;
use App\Http\Requests\Visitor\Auth\ResetPasswordRequest;
use App\Http\Requests\Visitor\Auth\SetPasswordRequest;

class AuthController extends Controller
{
    private const TOKEN_EXPIRY_MINUTES = 60;

    // ═══════════════════════════════════════════════
    // LOGIN
    // ═══════════════════════════════════════════════

    public function showLoginForm()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('visitor.index');
        }

        return view('visitor.pages.login');
    }

    public function login(LoginRequest $request)
    {
        $request->ensureIsNotRateLimited();

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (!Auth::guard('customer')->attempt($credentials, $remember)) {
            $request->incrementRateLimiter();

            User::where('email', $request->email)->first()?->incrementFailedAttempts();

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'These credentials do not match our records.',
                ]);
        }

        $customer = Auth::guard('customer')->user();

        if (!$customer->hasVerifiedEmail()) {
            Auth::guard('customer')->logout();

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Please verify your email address before logging in.',
                ])
                ->with('unverified_email', $customer->email);
        }

        // Ban / lock check at the login moment itself, not just on
        // subsequent requests via the CustomerAuthenticated middleware.
        if ($customer->isBanned()) {
            Auth::guard('customer')->logout();

            return back()->withErrors([
                'email' => 'Your account has been suspended. Contact support.',
            ]);
        }

        if ($customer->isLocked()) {
            Auth::guard('customer')->logout();

            return back()->withErrors([
                'email' => 'Account temporarily locked due to failed login attempts. Try again later.',
            ]);
        }

        $request->clearRateLimiter();
        $request->session()->regenerate();

        $customer->updateLastLogin($request->ip());

        return redirect()
            ->intended(route('visitor.index'))
            ->with('success', "Welcome back, {$customer->name}!");
    }

    // ═══════════════════════════════════════════════
    // LOGOUT
    // ═══════════════════════════════════════════════

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('visitor.index')
            ->with('success', 'You have been logged out successfully.');
    }

    // ═══════════════════════════════════════════════
    // EMAIL VERIFICATION
    // ═══════════════════════════════════════════════

    public function verifyEmail(Request $request, string $id, string $hash)
    {
        $customer = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($customer->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        if ($customer->hasVerifiedEmail()) {
            return redirect()
                ->route('visitor.login')
                ->with('success', 'Your email is already verified. Please log in.');
        }

        $customer->markEmailAsVerified();

        return redirect()
            ->route('visitor.login')
            ->with('success', 'Email verified successfully! You can now log in.');
    }

    public function verificationNotice(Request $request)
    {
        return view('visitor.pages.verify-email', [
            'email' => $request->query('email'),
        ]);
    }

    public function resendVerificationEmail(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $key = 'resend-verification:' . Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'email' => "Please wait {$seconds} seconds before requesting another verification email.",
            ]);
        }

        RateLimiter::hit($key, 600);

        $customer = User::where('email', $request->email)->first();

        if ($customer && !$customer->hasVerifiedEmail()) {
            $customer->sendEmailVerificationNotification();
        }

        // Same response regardless of match, to avoid email enumeration.
        return back()->with(
            'success',
            'If that email is registered and not yet verified, a new verification link has been sent.'
        );
    }

    // ═══════════════════════════════════════════════
    // REGISTER
    // ═══════════════════════════════════════════════

    public function showRegisterForm()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('visitor.index');
        }

        return view('visitor.pages.register');
    }

    public function register(RegisterRequest $request)
    {
        $customer = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'status'   => 'active',
        ]);

        $customer->sendEmailVerificationNotification();

        return redirect()
            ->route('verification.notice', ['email' => $customer->email])
            ->with('success', 'Account created! Please check your email to verify your address before logging in.');
    }

    // ═══════════════════════════════════════════════
    // FORGOT PASSWORD
    // ═══════════════════════════════════════════════

    public function forgotPasswordView()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('visitor.index');
        }

        return view('visitor.pages.forgot-password');
    }

    public function forgotPasswordAttempt(ForgotPasswordRequest $request)
    {
        $request->ensureIsNotRateLimited();

        $customer = User::where('email', $request->email)
            ->where('status', 'active')
            ->first();

        // Always show success message even if email not found —
        // prevents email enumeration, same pattern as Admin auth.
        if (!$customer) {
            $request->incrementRateLimiter();

            return back()->with(
                'success',
                'If that email exists in our system, we have sent a password reset link.'
            );
        }

        DB::table('password_reset_tokens')
            ->where('email', $customer->email)
            ->delete();

        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email'      => $customer->email,
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        $resetUrl = route('visitor.password.reset', [
            'token' => $token,
            'email' => $customer->email,
        ]);

        Mail::to($customer->email)->send(new CustomerPasswordResetMail(
            resetUrl: $resetUrl,
            customerName: $customer->name,
            expiresInMinutes: self::TOKEN_EXPIRY_MINUTES,
        ));

        $request->incrementRateLimiter();

        return back()->with(
            'success',
            'If that email exists in our system, we have sent a password reset link.'
        );
    }

    // ═══════════════════════════════════════════════
    // RESET PASSWORD (self-service, from Forgot Password)
    // ═══════════════════════════════════════════════

    public function resetPasswordView(Request $request, string $token)
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('visitor.index');
        }

        if (!$request->has('email') || !$token) {
            return redirect()
                ->route('visitor.password.request')
                ->with('error', 'Invalid password reset link.');
        }

        return view('visitor.pages.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPasswordAttempt(ResetPasswordRequest $request)
    {
        $tokenRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$tokenRecord) {
            return back()->withErrors([
                'email' => 'Invalid or expired reset link. Please request a new one.',
            ]);
        }

        $createdAt = \Carbon\Carbon::parse($tokenRecord->created_at);
        if ($createdAt->addMinutes(self::TOKEN_EXPIRY_MINUTES)->isPast()) {
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            return back()->withErrors([
                'email' => 'This reset link has expired. Please request a new one.',
            ]);
        }

        if (!Hash::check($request->token, $tokenRecord->token)) {
            return back()->withErrors([
                'email' => 'Invalid reset link. Please request a new one.',
            ]);
        }

        $customer = User::where('email', $request->email)
            ->where('status', 'active')
            ->first();

        if (!$customer) {
            return back()->withErrors([
                'email' => 'No active account found with this email.',
            ]);
        }

        $customer->update([
            'password' => Hash::make($request->password),
        ]);

        // Successfully following the emailed link is itself proof of email
        // ownership for admin-created accounts, so treat this as verification too.
        if (!$customer->hasVerifiedEmail()) {
            $customer->markEmailAsVerified();
        }

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return redirect()
            ->route('visitor.login')
            ->with('success', 'Password reset successfully. Please login with your new password.');
    }

    // ═══════════════════════════════════════════════
    // SET PASSWORD (first-time, from admin-created account)
    // — unchanged from earlier Customer module work —
    // ═══════════════════════════════════════════════

    public function setPasswordView(Request $request, string $token)
    {
        if (!$request->has('email') || !$token) {
            return redirect()
                ->route('visitor.index')
                ->with('error', 'Invalid password set-up link.');
        }

        return view('visitor.auth.set-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function setPasswordAttempt(SetPasswordRequest $request)
    {
        $tokenRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$tokenRecord) {
            return back()->withErrors([
                'email' => 'Invalid or expired link. Please contact support for a new one.',
            ]);
        }

        $createdAt = \Carbon\Carbon::parse($tokenRecord->created_at);
        if ($createdAt->addMinutes(self::TOKEN_EXPIRY_MINUTES)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return back()->withErrors([
                'email' => 'This link has expired. Please contact support for a new one.',
            ]);
        }

        if (!Hash::check($request->token, $tokenRecord->token)) {
            return back()->withErrors([
                'email' => 'Invalid link. Please contact support for a new one.',
            ]);
        }

        $customer = User::where('email', $request->email)->first();

        if (!$customer) {
            return back()->withErrors([
                'email' => 'No account found with this email.',
            ]);
        }

        $customer->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()
            ->route('visitor.login')
            ->with('success', 'Password set successfully. You can now log in.');
    }
}
