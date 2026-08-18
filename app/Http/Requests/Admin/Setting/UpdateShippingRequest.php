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
            'operation_areas'          => ['required', 'array', 'min:1'],
            'operation_areas.*'        => ['string', 'max:100'],
            'inside_area_charge'       => ['required', 'numeric', 'min:0'],
            'outside_area_charge'      => ['required', 'numeric', 'min:0'],
            'enable_free_shipping'     => ['nullable'],
            'free_shipping_threshold'  => ['nullable', 'numeric', 'min:0'],
            'default_processing_days'  => ['nullable', 'integer', 'min:0', 'max:60'],
            'shipping_note'            => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'operation_areas.required'      => 'Select at least one operation area.',
            'operation_areas.min'           => 'Select at least one operation area.',
            'inside_area_charge.required'   => 'In-area shipping charge is required.',
            'outside_area_charge.required'  => 'Out-of-area shipping charge is required.',
        ];
    }
}