<?php

namespace App\Http\Requests\Visitor\Auth;

use App\Rules\ReCaptcha;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
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
            'email' => ['required', 'string', 'email', 'max:255'],
            'g-recaptcha-response' => [
                'required',
                new ReCaptcha(
                    threshold: config('services.recaptcha.threshold', 0.5),
                    action: 'customer_forgot_password',
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'g-recaptcha-response.required' => 'reCAPTCHA verification failed. Please try again.',
        ];
    }

    public function ensureIsNotRateLimited(): void
    {
        $key = 'customer-forgot-password:'.$this->ip();

        if (! RateLimiter::tooManyAttempts($key, 3)) {
            return;
        }

        $seconds = RateLimiter::availableIn($key);

        throw ValidationException::withMessages([
            'email' => "Too many attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    public function incrementRateLimiter(): void
    {
        RateLimiter::hit('customer-forgot-password:'.$this->ip(), 600);
    }
}
