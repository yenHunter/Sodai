<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Restricts admin-entered link fields (banner/offer CTA buttons) to http(s)
 * URLs or app-relative paths, so a `javascript:` / `data:` link can never
 * reach an href on the storefront.
 */
class SafeUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || trim($value) === '') {
            return;
        }

        $url = trim($value);

        // Control characters / whitespace are used to smuggle schemes.
        if (preg_match('/[\x00-\x1F\x7F\s]/', $url) === 1) {
            $fail('The :attribute must be a valid link.');

            return;
        }

        // App-relative path ("/products"), but not protocol-relative ("//evil.test").
        if (str_starts_with($url, '/')) {
            if (! str_starts_with($url, '//')) {
                return;
            }

            $fail('The :attribute must be an http(s) URL or an app-relative path.');

            return;
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);
        $host = parse_url($url, PHP_URL_HOST);

        if (is_string($scheme) && in_array(strtolower($scheme), ['http', 'https'], true) && is_string($host) && $host !== '') {
            return;
        }

        $fail('The :attribute must be an http(s) URL or an app-relative path.');
    }
}
