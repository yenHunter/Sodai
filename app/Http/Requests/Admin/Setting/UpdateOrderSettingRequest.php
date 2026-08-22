<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateOrderSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'order_number_prefix' => ['required', 'string', 'max:10'],
            'auto_cancel_unpaid_hours' => ['nullable', 'integer', 'min:1', 'max:720'],
            'minimum_order_amount' => ['nullable', 'numeric', 'min:0'],
            'allow_guest_checkout' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'order_number_prefix.required' => 'Order number prefix is required (e.g. ORD-).',
        ];
    }
}
