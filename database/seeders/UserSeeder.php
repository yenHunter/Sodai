<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@ecommerce.com',
            'password' => Hash::make('password'), // password
            'role' => UserRole::ADMIN,
        ]);

        // 2. Vendor (Nike Official)
        User::create([
            'name' => 'Nike Official',
            'email' => 'vendor@nike.com',
            'password' => Hash::make('password'),
            'role' => UserRole::VENDOR,
        ]);

        // 3. Customer (John Doe)
        User::create([
            'name' => 'John Doe',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('password'),
            'role' => UserRole::CUSTOMER,
        ]);
    }
}
