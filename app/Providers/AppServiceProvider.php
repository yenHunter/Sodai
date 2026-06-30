<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ─────────────────────────────────────────────
        // CUSTOM BLADE DIRECTIVES FOR ADMIN PERMISSIONS
        // ─────────────────────────────────────────────

        // @admincan('permission.name')
        Blade::directive('admincan', function ($permission) {
            return "<?php if(auth('admin')->check() && auth('admin')->user()->can({$permission})): ?>";
        });

        // @endadmincan
        Blade::directive('endadmincan', function () {
            return "<?php endif; ?>";
        });

        // @admincanany(['perm1', 'perm2'])
        Blade::directive('admincanany', function ($permissions) {
            return "<?php if(auth('admin')->check() && auth('admin')->user()->canAny({$permissions})): ?>";
        });

        // @endadmincanany
        Blade::directive('endadmincanany', function () {
            return "<?php endif; ?>";
        });

        // @adminrole('role-name')
        Blade::directive('adminrole', function ($role) {
            return "<?php if(auth('admin')->check() && auth('admin')->user()->hasRole({$role})): ?>";
        });

        // @endadminrole
        Blade::directive('endadminrole', function () {
            return "<?php endif; ?>";
        });
    }
}