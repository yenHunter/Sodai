<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\ValidationRule;

class ReCaptcha implements ValidationRule
{
    public function __construct(
        // Minimum score threshold
        // 1.0 = definitely human, 0.0 = definitely bot
        // 0.5 is Google's recommended minimum
        private float  $threshold = 0.5,
        private string $action    = 'login',
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!app()->isProduction()) {
            Log::debug('ReCaptcha response', $response ?? []);
        }
        // Remove this block once you have valid production keys
        if (config('app.env') === 'local' && config('app.debug')) {
            Log::info('reCAPTCHA: Skipped in development mode');
            return;
        }
        // Handle empty token
        if (empty($value)) {
            Log::error('reCAPTCHA: Empty token received', [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            $fail('reCAPTCHA verification failed. Please try again.');
            return;
        }

        // Verify with Google
        try {
            $response = Http::timeout(10)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret'   => config('services.recaptcha.secret_key'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ])->json();

            Log::info('reCAPTCHA: Google API response', [
                'success' => $response['success'] ?? null,
                'score' => $response['score'] ?? null,
                'action' => $response['action'] ?? null,
                'error_codes' => $response['error-codes'] ?? [],
            ]);
        } catch (\Exception $e) {
            // If Google API is unreachable - fail open or closed
            // Fail open (allow login) to avoid locking out real admins
            Log::error('reCAPTCHA: Google API call failed', [
                'error' => $e->getMessage(),
                'ip' => request()->ip(),
            ]);
            return;
        }

        // Check success
        if (empty($response['success']) || !$response['success']) {
            Log::warning('reCAPTCHA: Verification failed', [
                'error_codes' => $response['error-codes'] ?? [],
                'ip' => request()->ip(),
            ]);
            $fail('reCAPTCHA verification failed. Please try again.');
            return;
        }

        // ── v3 Specific Checks ──

        // Check score (v3 only - v2 does not return score)
        if (isset($response['score']) && $response['score'] < $this->threshold) {
            Log::warning('reCAPTCHA: Score too low', [
                'score' => $response['score'],
                'threshold' => $this->threshold,
                'ip' => request()->ip(),
            ]);
            $fail('reCAPTCHA verification failed. Suspicious activity detected.');
            return;
        }

        // Check action matches (v3 only - prevents token reuse across actions)
        if (
            isset($response['action']) &&
            $response['action'] !== $this->action
        ) {
            Log::warning('reCAPTCHA: Action mismatch', [
                'expected' => $this->action,
                'received' => $response['action'],
                'ip' => request()->ip(),
            ]);
            $fail('reCAPTCHA verification failed. Invalid action.');
            return;
        }
    }
}
