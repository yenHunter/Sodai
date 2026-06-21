<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token'    => ['required', 'string'],
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required'             => 'Invalid reset link.',
            'email.required'             => 'Email address is required.',
            'password.required'          => 'New password is required.',
            'password.confirmed'         => 'Passwords do not match.',
        ];
    }
}