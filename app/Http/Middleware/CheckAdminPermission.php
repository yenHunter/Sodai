<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $admin = auth('admin')->user();

        // Super admin bypasses all permission checks
        if ($admin->hasRole('super-admin')) {
            return $next($request);
        }

        // Check if admin has ANY of the given permissions
        foreach ($permissions as $permission) {
            if ($admin->hasPermissionTo($permission, 'admin')) {
                return $next($request);
            }
        }

        // If AJAX/API request
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'You do not have permission to perform this action.'
            ], 403);
        }

        // If web request
        abort(403, 'You do not have permission to access this page.');
    }
}