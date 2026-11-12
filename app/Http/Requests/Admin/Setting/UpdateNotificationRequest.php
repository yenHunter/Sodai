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
            'abandoned_cart_enabled' => ['nullable'],
            'abandoned_cart_hours' => ['nullable', 'integer', 'min:1', 'max:720'],
            'queue_depth_threshold' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'queue_alert_cooldown_minutes' => ['nullable', 'integer', 'min:1', 'max:10080'],
            'queue_monitor_queues' => [
                'nullable',
                'string',
                'max:500',
                'regex:/^\s*[A-Za-z0-9_.-]+:[A-Za-z0-9_.-]+(?:\s*,\s*[A-Za-z0-9_.-]+:[A-Za-z0-9_.-]+)*\s*$/',
            ],
            'weekly_sales_summary_enabled' => ['nullable'],
            'monthly_sales_summary_enabled' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'admin_alert_email.required' => 'Admin alert email is required.',
            'queue_monitor_queues.regex' => 'Each monitored queue must use the connection:queue format, separated by commas.',
        ];
    }
}
