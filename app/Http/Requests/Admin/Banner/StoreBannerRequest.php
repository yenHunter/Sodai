<?php

namespace App\Http\Requests\Admin\Banner;

use App\Models\Banner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'button_target' => ['nullable', 'in:'.implode(',', Banner::TARGETS)],
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'mobile_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'position' => ['required', 'in:'.implode(',', Banner::POSITIONS)],
            'text_position' => ['nullable', 'in:'.implode(',', Banner::TEXT_POSITIONS)],
            'is_active' => ['required'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'Banner image is required.',
            'image.mimes' => 'Image must be jpeg, jpg, png or webp.',
            'image.max' => 'Image size cannot exceed 4MB.',
            'position.required' => 'Banner position is required.',
            'position.in' => 'Invalid banner position selected.',
            'is_active.required' => 'Status is required.',
            'expires_at.after_or_equal' => 'Expiry date must be after or equal to the start date.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sort_order' => $this->input('sort_order') ?? 0,
        ]);
    }
}
