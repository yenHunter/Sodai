<?php

namespace App\Http\Requests\Visitor\Checkout;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Guest checkout is allowed only if the setting permits it.
        if (Auth::guard('customer')->check()) {
            return true;
        }

        return (bool) setting('order', 'allow_guest_checkout', false);
    }

    public function rules(): array
    {
        return [
            'address_id' => ['nullable', 'integer', 'exists:addresses,id'],

            // Required only when not selecting a saved address (guest, or "new address" option)
            'shipping_name' => ['required_without:address_id', 'nullable', 'string', 'max:255'],
            'shipping_email' => ['required_without:address_id', 'nullable', 'email', 'max:255'],
            'shipping_phone' => ['required_without:address_id', 'nullable', 'string', 'max:30'],
            'shipping_address' => ['required_without:address_id', 'nullable', 'string'],
            'shipping_city' => ['required_without:address_id', 'nullable', 'string', 'max:100'],
            'shipping_state' => ['required_without:address_id', 'nullable', 'string', 'max:100'],
            'shipping_zip' => ['required_without:address_id', 'nullable', 'string', 'max:20'],
            'shipping_country' => ['required_without:address_id', 'nullable', 'string', 'max:100'],

            'payment_method' => ['required', 'in:cod'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_name.required_without' => 'Full name is required.',
            'shipping_email.required_without' => 'Email address is required.',
            'shipping_phone.required_without' => 'Phone number is required.',
            'shipping_address.required_without' => 'Address is required.',
            'shipping_city.required_without' => 'City is required.',
            'shipping_state.required_without' => 'State is required.',
            'shipping_zip.required_without' => 'ZIP code is required.',
            'shipping_country.required_without' => 'Country is required.',
            'payment_method.required' => 'Please select a payment method.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'coupon_code' => $this->input('coupon_code') ? strtoupper(trim($this->input('coupon_code'))) : null,
        ]);
    }
}
