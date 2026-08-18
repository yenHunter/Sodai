<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'cod_enabled'            => ['nullable'],
            'cod_extra_fee'          => ['nullable', 'numeric', 'min:0'],
            'bank_transfer_enabled'  => ['nullable'],
            'bank_details'           => ['nullable', 'string', 'max:2000'],
            'online_payment_enabled' => ['nullable'],
        ];
    }
}