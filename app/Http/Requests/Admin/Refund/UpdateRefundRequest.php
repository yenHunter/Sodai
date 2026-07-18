<?php

namespace App\Http\Requests\Admin\Refund;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateRefundRequest extends FormRequest
{
    public function authorize(): bool
    {
        $refund = $this->route('refund');
        return Auth::guard('admin')->check() && $refund && $refund->isEditable();
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
            'amount.required'   => 'Refund amount is required.',
            'reason.required'   => 'Please provide a reason for the refund.',
        ];
    }
}