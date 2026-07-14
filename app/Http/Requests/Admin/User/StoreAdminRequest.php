<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:admins,email'],
            'phone'     => ['nullable', 'string', 'max:30'],
            'password'  => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'role_id'   => ['required', 'integer', 'exists:roles,id'],
            'is_active' => ['required'],
            'avatar'    => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Name is required.',
            'email.required'     => 'Email is required.',
            'email.unique'       => 'This email is already registered.',
            'password.required'  => 'Password is required.',
            'password.confirmed' => 'Passwords do not match.',
            'role_id.required'   => 'Please select a role.',
            'role_id.exists'     => 'Selected role does not exist.',
            'is_active.required' => 'Status is required.',
        ];
    }
}