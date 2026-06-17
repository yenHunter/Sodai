<?php

namespace App\Http\Requests\Admin;

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
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();
    }

    public function ensureIsNotRateLimited(): void
    {
        $key = 'admin-login:' . Str::lower($this->input('email'))
               . '|' . $this->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Try again in {$seconds} seconds.",
            ]);
        }

        RateLimiter::hit($key, 300); // 5 minutes decay
    }

    protected function failedValidation($validator)
    {
        $key = 'admin-login:' . Str::lower($this->input('email'))
               . '|' . $this->ip();
        RateLimiter::hit($key, 300);

        parent::failedValidation($validator);
    }
}