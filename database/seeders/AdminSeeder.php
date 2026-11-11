<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Never create known-password accounts on a deployed environment.
        if (app()->environment('production')) {
            $this->command?->warn('AdminSeeder skipped: demo credentials are disabled in production.');

            return;
        }

        $password = env('SEED_ADMIN_PASSWORD', 'Admin#123');

        // Super Admin
        $admin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@cyberjatra.com',
            'password' => Hash::make($password),
            'phone' => '+8801700000000',
            'is_active' => true,
        ]);
        $admin->assignRole('super-admin');

        // Sample Manager (optional - remove if not needed)
        $manager = Admin::create([
            'name' => 'Manager',
            'email' => 'manager@cyberjatra.com',
            'password' => Hash::make(env('SEED_MANAGER_PASSWORD', 'Manager#123')),
            'phone' => '+8801700000001',
            'is_active' => true,
        ]);
        $manager->assignRole('manager');
    }
}
