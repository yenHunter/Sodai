<?php

namespace App\Http\Requests\Admin\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');
        return Auth::guard('admin')->check() && $order && $order->isEditable();
    }

    public function rules(): array
    {
        return [
            'user_id'          => ['required', 'integer', 'exists:users,id'],

            'shipping_name'    => ['required', 'string', 'max:255'],
            'shipping_email'   => ['required', 'email', 'max:255'],
            'shipping_phone'   => ['required', 'string', 'max:30'],
            'shipping_address' => ['required', 'string'],
            'shipping_city'    => ['required', 'string', 'max:100'],
            'shipping_state'   => ['required', 'string', 'max:100'],
            'shipping_zip'     => ['required', 'string', 'max:20'],
            'shipping_country' => ['required', 'string', 'max:100'],

            'items'                => ['required', 'array', 'min:1'],
            'items.*.product_id'   => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'     => ['required', 'integer', 'min:1'],

            'discount_amount'  => ['nullable', 'numeric', 'min:0'],
            'shipping_charge'  => ['nullable', 'numeric', 'min:0'],
            'tax_amount'       => ['nullable', 'numeric', 'min:0'],
            'coupon_code'      => ['nullable', 'string', 'max:50', 'exists:coupons,code'],
            'notes'            => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Please select a customer.',
            'items.required'   => 'Add at least one product to the order.',
            'items.min'        => 'Add at least one product to the order.',
            'coupon_code.exists' => 'This coupon code does not exist.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'discount_amount' => $this->input('discount_amount') === '' ? null : $this->input('discount_amount'),
            'shipping_charge' => $this->input('shipping_charge') === '' ? null : $this->input('shipping_charge'),
            'tax_amount'      => $this->input('tax_amount') === '' ? null : $this->input('tax_amount'),
            'coupon_code'     => $this->input('coupon_code') ? strtoupper(trim($this->input('coupon_code'))) : null,
        ]);
    }
}