<?php
// database/seeders/AdminSeeder.php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'name'      => 'Super Admin',
            'email'     => 'admin@admin.com',
            'password'  => Hash::make('Admin#123'),
            'phone'     => '01700000000',
            'is_active' => true,
        ]);
    }
}