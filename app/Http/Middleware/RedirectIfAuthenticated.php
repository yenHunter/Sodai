<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        // If no guard specified, use default
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            switch ($guard) {

                // ── Admin Guard ──
                case 'admin':
                    if (Auth::guard('admin')->check()) {
                        return redirect()->route('admin.dashboard');
                    }
                    break;

                // ── Customer Guard ──
                case 'customer':
                    if (Auth::guard('customer')->check()) {
                        return redirect()->route('visitor.home');
                    }
                    break;

                // ── Default Guard ──
                default:
                    if (Auth::guard($guard)->check()) {
                        return redirect('/');
                    }
                    break;
            }
        }

        return $next($request);
    }
}