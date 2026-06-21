<?php
// app/Rules/ReCaptcha.php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class ReCaptcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Handle empty value
        if (empty($value)) {
            $fail('Please complete the reCAPTCHA verification.');
            return;
        }

        // Verify with Google
        try {
            $response = Http::timeout(10)
                ->get('https://www.google.com/recaptcha/api/siteverify', [
                    'secret'   => config('services.recaptcha.secret_key'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ])->json();

        } catch (\Exception $e) {
            // If Google API is unreachable
            $fail('reCAPTCHA verification failed. Please try again.');
            return;
        }

        // ✅ $fail is a Closure — must be CALLED, not assigned
        if (empty($response['success']) || !$response['success']) {
            $fail('Google reCAPTCHA verification failed. Please try again.');
        }
    }
}