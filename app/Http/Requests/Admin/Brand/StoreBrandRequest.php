<?php

namespace App\Http\Requests\Admin\Brand;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'name'        => [
                'required',
                'string',
                'max:100',
                'unique:brands,name',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'website'     => [
                'nullable',
                'url',
                'max:255',
            ],
            'logo'        => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048',
            ],
            'is_active'   => [
                'required',
            ],
            'sort_order'  => [
                'nullable',
                'integer',
                'min:0',
                'max:9999',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Brand name is required.',
            'name.unique'        => 'This brand name already exists.',
            'name.max'           => 'Brand name cannot exceed 100 characters.',
            'website.url'        => 'Please enter a valid URL (e.g. https://example.com).',
            'logo.image'         => 'File must be an image.',
            'logo.mimes'         => 'Logo must be jpeg, jpg, png or webp.',
            'logo.max'           => 'Logo size cannot exceed 2MB.',
            'is_active.required' => 'Status is required.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sort_order' => $this->input('sort_order') ?? 0,
        ]);
    }
}