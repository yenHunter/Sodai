<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateTaxRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'tax_enabled'         => ['nullable'],
            'tax_label'           => ['nullable', 'string', 'max:50'],
            'tax_rate'            => ['required', 'numeric', 'min:0', 'max:100'],
            'prices_include_tax'  => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'tax_rate.required' => 'Tax rate is required (use 0 if not applicable).',
        ];
    }
}