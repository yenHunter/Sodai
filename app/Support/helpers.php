<?php

use App\Models\Setting;
use App\Support\Currency;

if (! function_exists('setting')) {
    /**
     * Fetch a stored setting value. Usage: setting('company', 'name', 'Sodai')
     */
    function setting(string $group, string $key, mixed $default = null): mixed
    {
        return Setting::get($group, $key, $default);
    }
}

if (! function_exists('money')) {
    /**
     * Format an amount with the configured currency symbol and symbol position.
     * Usage: money(1299.5) => "$1,299.50" (or "1,299.50৳" when the symbol goes after).
     */
    function money(mixed $amount, ?int $decimals = null, ?string $currency = null): string
    {
        return Currency::format($amount, $decimals, $currency);
    }
}

if (! function_exists('format_price')) {
    /**
     * Alias of money(), kept for readability in views and services.
     */
    function format_price(mixed $amount, ?int $decimals = null, ?string $currency = null): string
    {
        return Currency::format($amount, $decimals, $currency);
    }
}

if (! function_exists('currency_symbol')) {
    function currency_symbol(?string $currency = null): string
    {
        return Currency::symbol($currency);
    }
}

if (! function_exists('currency_position')) {
    function currency_position(): string
    {
        return Currency::position();
    }
}
