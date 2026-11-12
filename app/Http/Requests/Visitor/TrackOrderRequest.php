<?php

namespace App\Http\Requests\Visitor;

use Illuminate\Foundation\Http\FormRequest;

class TrackOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_number' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'order_number.required' => 'Order number is required.',
            'email.required' => 'Email address is required.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'order_number' => trim((string) $this->input('order_number')),
            'email' => trim((string) $this->input('email')),
        ]);
    }
}
