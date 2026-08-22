<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'admin_alert_email' => ['required', 'email', 'max:150'],
            'notify_new_order' => ['nullable'],
            'notify_low_stock' => ['nullable'],
            'notify_new_review' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'admin_alert_email.required' => 'Admin alert email is required.',
        ];
    }
}
