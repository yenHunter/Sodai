<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email address is required.',
            'email.email'    => 'Please enter a valid email address.',
        ];
    }

    public function ensureIsNotRateLimited(): void
    {
        $key = 'admin-forgot-password:' . $this->ip();

        if (!RateLimiter::tooManyAttempts($key, 3)) {
            return;
        }

        $seconds = RateLimiter::availableIn($key);

        throw ValidationException::withMessages([
            'email' => "Too many attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    public function incrementRateLimiter(): void
    {
        // Decay of 10 minutes
        RateLimiter::hit(
            'admin-forgot-password:' . $this->ip(),
            600
        );
    }
}