<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateDesignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'logo_dark' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:png,jpg,jpeg,ico,webp', 'max:512'],
            'login_bg' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'remove_logo' => ['nullable'],
            'remove_logo_dark' => ['nullable'],
            'remove_favicon' => ['nullable'],
            'remove_login_bg' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.mimes' => 'Logo must be png, jpg, jpeg, webp or svg.',
            'favicon.mimes' => 'Favicon must be png, jpg, jpeg, ico or webp.',
            'login_bg.mimes' => 'Login background must be png, jpg, jpeg or webp.',
        ];
    }
}
