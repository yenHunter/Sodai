<?php

namespace App\Http\Controllers\Visitor;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\Visitor\Auth\SetPasswordRequest;

class AuthController extends Controller
{
    private const TOKEN_EXPIRY_MINUTES = 60;

    // ═══════════════════════════════════════════════
    // SET PASSWORD (first-time, from admin-created account)
    // ═══════════════════════════════════════════════

    public function setPasswordView(Request $request, string $token)
    {
        if (!$request->has('email') || !$token) {
            return redirect()
                ->route('visitor.index')
                ->with('error', 'Invalid password set-up link.');
        }

        return view('visitor.pages.set-password', [
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
            ->route('visitor.index')
            ->with('success', 'Password set successfully. You can now log in once customer login is available.');
    }
}