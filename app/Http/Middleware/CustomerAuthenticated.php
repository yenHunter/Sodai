<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('customer')->check()) {
            return redirect()
                ->route('login')
                ->with('error', 'Please login to continue.');
        }

        $user = Auth::guard('customer')->user();

        if ($user->isBanned()) {
            Auth::guard('customer')->logout();
            return redirect()
                ->route('login')
                ->with('error', 'Your account has been suspended. Contact support.');
        }

        if ($user->isLocked()) {
            Auth::guard('customer')->logout();
            return redirect()
                ->route('login')
                ->with('error', 'Account temporarily locked. Try again later.');
        }

        return $next($request);
    }
}