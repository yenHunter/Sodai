<?php
// config/auth.php

return [
    'defaults' => [
        'guard'     => 'customer',
        'passwords' => 'customers',
    ],

    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],

        // Customer guard
        'customer' => [
            'driver'   => 'session',
            'provider' => 'customers',
        ],

        // Admin guard
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
            'model'  => App\Models\User::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Admin::class,
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
            'expire'   => 30,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
