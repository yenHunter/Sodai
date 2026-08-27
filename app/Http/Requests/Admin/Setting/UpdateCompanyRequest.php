<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'currency' => ['required', 'string', 'max:5'],
            'currency_symbol_position' => ['required', 'in:before,after'],
            'timezone' => ['required', 'string', 'max:50'],
            'map_embed_url' => ['nullable', 'url', 'max:1000', 'starts_with:https://www.google.com/maps/embed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Company name is required.',
            'email.required' => 'Support email is required.',
            'phone.required' => 'Support phone is required.',
            'address.required' => 'Company address is required.',
            'currency.required' => 'Currency code is required.',
            'map_embed_url.starts_with' => 'Paste a valid Google Maps embed URL (starts with https://www.google.com/maps/embed).',
        ];
    }
}
