<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Deterministic account for the Playwright authentication tests.
 *
 * Only runs when DatabaseSeeder is invoked with E2E_SEED=true (the CI e2e job
 * and the documented local command), and never in production. The TOTP secret
 * and recovery codes are fixed so browser tests can calculate valid codes.
 */
class E2eSeeder extends Seeder
{
    public const EMAIL = 'e2e-admin@example.com';

    public const PASSWORD = 'E2ePassword#123';

    public const TOTP_SECRET = 'JBSWY3DPEHPK3PXPJBSWY3DPEHPK3PXP';

    /** @var array<int, string> */
    public const RECOVERY_CODES = ['E2EAA-BBBBB', 'E2ECCC-DDDDD'];

    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->warn('E2eSeeder skipped: test accounts are disabled in production.');

            return;
        }

        $admin = Admin::updateOrCreate(
            ['email' => self::EMAIL],
            [
                'name' => 'E2E Admin',
                'password' => Hash::make(self::PASSWORD),
                'is_active' => true,
            ],
        );

        $admin->syncRoles(['super-admin']);

        $admin->forceFill([
            'two_factor_secret' => self::TOTP_SECRET,
            'two_factor_recovery_codes' => self::RECOVERY_CODES,
            'two_factor_confirmed_at' => now(),
        ])->save();
    }
}
