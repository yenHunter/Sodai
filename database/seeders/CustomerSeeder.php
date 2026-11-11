<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Never create known-password accounts on a deployed environment.
        if (app()->environment('production')) {
            $this->command?->warn('CustomerSeeder skipped: demo credentials are disabled in production.');

            return;
        }

        User::create([
            'name' => 'Demo Customer',
            'email' => env('SEED_CUSTOMER_EMAIL', 'customer@example.com'),
            'phone' => '+8801515283693',
            'password' => Hash::make(env('SEED_CUSTOMER_PASSWORD', 'Customer#123')),
            'status' => 'active',
        ]);
    }
}
