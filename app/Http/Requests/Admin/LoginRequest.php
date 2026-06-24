<?php

namespace App\Http\Requests\Admin;

use App\Rules\ReCaptcha;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => [
                'required',
                'string',
                'email',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
            ],
            // ── v3: pass action name and threshold ──
            'g-recaptcha-response' => [
                'required',
                new ReCaptcha(
                    threshold: config('services.recaptcha.threshold', 0.5),
                    action   : 'admin_login',
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'                => 'Email address is required.',
            'email.email'                   => 'Please enter a valid email address.',
            'password.required'             => 'Password is required.',
            'password.min'                  => 'Password must be at least 6 characters.',
            'g-recaptcha-response.required' => 'reCAPTCHA verification failed. Please try again.',
        ];
    }

    // ─────────────────────────────────────────────
    // RATE LIMITING
    // ─────────────────────────────────────────────

    public function ensureIsNotRateLimited(): void
    {
        $key = $this->throttleKey();

        if (!RateLimiter::tooManyAttempts($key, 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($key);

        throw ValidationException::withMessages([
            'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    public function incrementRateLimiter(): void
    {
        RateLimiter::hit($this->throttleKey(), 300);
    }

    public function clearRateLimiter(): void
    {
        RateLimiter::clear($this->throttleKey());
    }

    public function throttleKey(): string
    {
        return 'admin-login:'
            . Str::lower($this->input('email'))
            . '|'
            . $this->ip();
    }
}