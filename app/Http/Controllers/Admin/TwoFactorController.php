<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\Admin\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TwoFactorController extends Controller
{
    public function __construct(
        private TwoFactorService $twoFactor
    ) {}

    /**
     * Setup screen (not enabled yet) or current status (enabled).
     */
    public function show(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if ($admin->hasTwoFactorEnabled()) {
            return view('admin.settings.users.two-factor', [
                'enabled' => true,
                'admin' => $admin,
            ]);
        }

        $secret = $request->session()->get('two_factor_setup_secret');

        if (! is_string($secret) || $secret === '') {
            $secret = $this->twoFactor->generateSecret();
            $request->session()->put('two_factor_setup_secret', $secret);
        }

        return view('admin.settings.users.two-factor', [
            'enabled' => false,
            'admin' => $admin,
            'secret' => $secret,
            'otpauthUrl' => $this->twoFactor->otpauthUrl($admin, $secret),
        ]);
    }

    public function enable(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:10'],
        ]);

        $admin = Auth::guard('admin')->user();
        $secret = $request->session()->get('two_factor_setup_secret');

        if (! is_string($secret) || $secret === '' || ! $this->twoFactor->verify($secret, $request->code)) {
            return back()->withErrors([
                'code' => 'That code is invalid. Check your authenticator app and try again.',
            ]);
        }

        $recoveryCodes = $this->twoFactor->generateRecoveryCodes();

        $admin->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => $recoveryCodes,
            'two_factor_confirmed_at' => now(),
        ])->save();

        $request->session()->forget('two_factor_setup_secret');

        return redirect()
            ->route('admin.two-factor.show')
            ->with('success', 'Two-factor authentication is now enabled.')
            ->with('recovery_codes', $recoveryCodes);
    }

    public function disable(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $admin = Auth::guard('admin')->user();

        if (! Hash::check($request->password, $admin->password)) {
            return back()->withErrors([
                'password' => 'The password is incorrect.',
            ]);
        }

        $admin->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $request->session()->forget('two_factor_setup_secret');

        return redirect()
            ->route('admin.two-factor.show')
            ->with('success', 'Two-factor authentication has been disabled.');
    }

    public function challenge(Request $request)
    {
        if (! $request->session()->has('login.2fa.admin_id')) {
            return redirect()->route('admin.login.view');
        }

        return view('admin.auth.two-factor-challenge');
    }

    public function verifyChallenge(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:20'],
        ]);

        $adminId = $request->session()->get('login.2fa.admin_id');
        $admin = $adminId ? Admin::active()->find($adminId) : null;

        if (! $admin || ! $admin->hasTwoFactorEnabled()) {
            $request->session()->forget(['login.2fa.admin_id', 'login.2fa.remember']);

            return redirect()
                ->route('admin.login.view')
                ->with('error', 'Your sign-in session expired. Please log in again.');
        }

        $valid = $this->twoFactor->verify($admin->two_factor_secret, $request->code)
            || $this->consumeRecoveryCode($admin, $request->code);

        if (! $valid) {
            return back()->withErrors([
                'code' => 'The authentication code is invalid.',
            ]);
        }

        $remember = (bool) $request->session()->pull('login.2fa.remember', false);
        $request->session()->forget('login.2fa.admin_id');

        Auth::guard('admin')->login($admin, $remember);
        $request->session()->regenerate();

        $admin->updateLastLogin($request->ip());

        return redirect()
            ->intended(route('admin.dashboard'))
            ->with('success', "Welcome back, {$admin->name}!");
    }

    /**
     * Recovery codes are single-use; a used code is removed immediately.
     */
    private function consumeRecoveryCode(Admin $admin, string $code): bool
    {
        $codes = $admin->two_factor_recovery_codes ?? [];
        $candidate = strtoupper(trim($code));

        foreach ($codes as $index => $stored) {
            if (hash_equals(strtoupper((string) $stored), $candidate)) {
                unset($codes[$index]);

                $admin->forceFill([
                    'two_factor_recovery_codes' => array_values($codes),
                ])->save();

                return true;
            }
        }

        return false;
    }
}
