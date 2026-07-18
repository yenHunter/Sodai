<?php

namespace App\Http\Requests\Admin\Refund;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreRefundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'amount'   => ['required', 'numeric', 'min:0.01'],
            'reason'   => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'Please select an order.',
            'order_id.exists'   => 'Selected order does not exist.',
            'amount.required'   => 'Refund amount is required.',
            'amount.min'        => 'Refund amount must be greater than 0.',
            'reason.required'   => 'Please provide a reason for the refund.',
        ];
    }
}