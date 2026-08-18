<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Fetch a stored setting value. Usage: setting('company', 'name', 'Sodai')
     */
    function setting(string $group, string $key, mixed $default = null): mixed
    {
        return Setting::get($group, $key, $default);
    }
}