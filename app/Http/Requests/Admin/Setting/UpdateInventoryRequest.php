<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'default_low_stock_threshold' => ['required', 'integer', 'min:0', 'max:1000'],
            'hide_out_of_stock_products' => ['nullable'],
            'allow_backorders' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'default_low_stock_threshold.required' => 'Default low-stock threshold is required.',
        ];
    }
}
