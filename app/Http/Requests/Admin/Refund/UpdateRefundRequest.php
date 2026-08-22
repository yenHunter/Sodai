<?php

namespace App\Http\Requests\Admin\Refund;

use App\Models\Order;
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
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    $order = Order::find($this->input('order_id'));
                    if (! $order) {
                        return;
                    }

                    $currentRefundId = $this->route('refund')->id;
                    $alreadyCommitted = (float) $order->refunds()
                        ->whereIn('status', ['pending', 'approved'])
                        ->where('id', '!=', $currentRefundId)
                        ->sum('amount');

                    $remaining = (float) $order->total_amount - $alreadyCommitted;
                    if ($value > $remaining) {
                        $fail("Refund amount cannot exceed the remaining refundable balance of \${$remaining}.");
                    }
                },
            ],
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'Please select an order.',
            'amount.required' => 'Refund amount is required.',
            'reason.required' => 'Please provide a reason for the refund.',
        ];
    }
}
