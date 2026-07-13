<?php

namespace App\Http\Requests\Admin\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
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
            'coupon_code'      => ['nullable', 'string', 'max:50'],
            'notes'            => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'         => 'Please select a customer.',
            'user_id.exists'           => 'Selected customer does not exist.',
            'items.required'           => 'Add at least one product to the order.',
            'items.min'                => 'Add at least one product to the order.',
            'items.*.product_id.exists' => 'One or more products in the cart no longer exist.',
            'items.*.quantity.min'     => 'Quantity must be at least 1.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'discount_amount' => $this->input('discount_amount') ?: 0,
            'shipping_charge' => $this->input('shipping_charge') ?: 0,
            'tax_amount'      => $this->input('tax_amount') ?: 0,
        ]);
    }
}