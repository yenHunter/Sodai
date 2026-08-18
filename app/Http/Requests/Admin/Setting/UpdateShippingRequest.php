<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateShippingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'flat_rate'                => ['required', 'numeric', 'min:0'],
            'enable_free_shipping'     => ['nullable'],
            'free_shipping_threshold'  => ['nullable', 'numeric', 'min:0'],
            'default_processing_days'  => ['nullable', 'integer', 'min:0', 'max:60'],
            'shipping_note'            => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'flat_rate.required' => 'Flat shipping rate is required.',
        ];
    }
}