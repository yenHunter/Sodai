<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'invoice_prefix' => ['required', 'string', 'max:10'],
            'invoice_starting_number' => ['required', 'integer', 'min:1'],
            'invoice_footer_note' => ['nullable', 'string', 'max:1000'],
            'show_tax_breakdown' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_prefix.required' => 'Invoice prefix is required (e.g. INV-).',
            'invoice_starting_number.required' => 'Starting invoice number is required.',
        ];
    }
}
