<?php

return [
    'defaults' => [
        'guard' => 'customer',      // default guard
        'passwords' => 'customers',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        // Customer Guard
        'customer' => [
            'driver'   => 'session',
            'provider' => 'customers',
        ],

        // Admin Guard
        'admin' => [
            'driver'   => 'session',
            'provider' => 'admins',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\User::class,
        ],

        'customers' => [
            'driver' => 'eloquent',
            'model'  => App\Models\User::class,  // Same users table
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Admin::class, // Separate admins table
        ],
    ],

    'passwords' => [
        'customers' => [
            'provider' => 'customers',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],

        'admins' => [
            'provider' => 'admins',
            'table'    => 'admin_password_reset_tokens',
            'expire'   => 30,    // Shorter for admin
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];