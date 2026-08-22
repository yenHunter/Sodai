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
        User::create([
            'name' => 'Firoz Ebna Jobaier',
            'email' => 'firoz.jobaier@gmail.com',
            'phone' => '+8801515283693',
            'password' => Hash::make('Customer#123'),
            'status' => 'active',
        ]);
    }
}
