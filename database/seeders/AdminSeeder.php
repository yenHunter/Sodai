<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $admin = Admin::create([
            'name'      => 'Super Admin',
            'email'     => 'admin@cyberjatra.com',
            'password'  => Hash::make('Admin#123'),
            'phone'     => '+8801700000000',
            'is_active' => true,
        ]);
        $admin->assignRole('super-admin');

        // Sample Manager (optional - remove if not needed)
        $manager = Admin::create([
            'name'      => 'Manager',
            'email'     => 'manager@cyberjatra.com',
            'password'  => Hash::make('Manager#123'),
            'phone'     => '+8801700000001',
            'is_active' => true,
        ]);
        $manager->assignRole('manager');
    }
}