<?php

namespace App\Http\Requests\Admin\Customer;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')->id;

        return [
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($customerId)],
            'phone'  => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($customerId)],
            'status' => ['required', 'in:active,inactive'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'   => 'Customer name is required.',
            'email.required'  => 'Email is required.',
            'email.unique'    => 'This email is already registered.',
            'phone.unique'    => 'This phone number is already registered.',
            'status.required' => 'Status is required.',
        ];
    }
}