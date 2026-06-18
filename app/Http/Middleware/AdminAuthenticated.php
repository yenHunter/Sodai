<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if admin is logged in
        if (!Auth::guard('admin')->check()) {
            return redirect()
                ->route('admin.login')
                ->with('error', 'Please login to access admin panel.');
        }

        $admin = Auth::guard('admin')->user();

        // Check if admin account is active
        if (!$admin->isActive()) {
            Auth::guard('admin')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('admin.login')
                ->with('error', 'Your account has been deactivated. Contact super admin.');
        }

        return $next($request);
    }
}