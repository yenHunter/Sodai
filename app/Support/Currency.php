<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Formats monetary amounts with the currency configured in Settings
 * (Company group: currency, currency_symbol_position, optional currency_decimals).
 */
class Currency
{
    /** Fallback symbol per ISO currency code; unknown codes fall back to the code itself. */
    public const SYMBOLS = [
        'BDT' => '৳',
        'EUR' => '€',
        'GBP' => '£',
        'INR' => '₹',
        'USD' => '$',
    ];

    public static function code(?string $code = null): string
    {
        $code ??= (string) Setting::get('company', 'currency', 'USD');
        $code = strtoupper(trim($code));

        return $code !== '' ? $code : 'USD';
    }

    public static function symbol(?string $code = null): string
    {
        $code = static::code($code);

        return self::SYMBOLS[$code] ?? $code;
    }

    /**
     * Whether the symbol is printed before ("$10.00") or after ("10.00$") the amount.
     */
    public static function position(): string
    {
        return Setting::get('company', 'currency_symbol_position', 'before') === 'after'
            ? 'after'
            : 'before';
    }

    public static function decimals(): int
    {
        return max(0, min(4, (int) Setting::get('company', 'currency_decimals', 2)));
    }

    public static function format(mixed $amount, ?int $decimals = null, ?string $code = null): string
    {
        $value = number_format((float) ($amount ?? 0), $decimals ?? static::decimals());
        $symbol = static::symbol($code);

        // Unknown currencies fall back to the ISO code, which needs a separator.
        $glue = isset(self::SYMBOLS[static::code($code)]) ? '' : ' ';

        return static::position() === 'after'
            ? $value.$glue.$symbol
            : $symbol.$glue.$value;
    }
}
