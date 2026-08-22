<?php

namespace App\Http\Requests\Admin\Product;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],

            // ── Relationships ──
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    $category = Category::find($value);
                    if ($category && $category->hasChildren()) {
                        $fail('Products can only be assigned to a sub-category, not a parent category.');
                    }
                },
            ],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],

            // ── Media ──
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],

            // ── Status ──
            'is_active' => ['required'],
            'is_featured' => ['nullable'],

            // ── SEO Meta ──
            'meta' => ['nullable', 'array'],
            'meta.meta_title' => ['nullable', 'string', 'max:255'],
            'meta.meta_description' => ['nullable', 'string', 'max:500'],
            'meta.meta_keywords' => ['nullable', 'string', 'max:255'],

            // ── Tags ──
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],

            // ── Related Products ──
            'related_products' => ['nullable', 'array'],
            'related_products.*' => ['integer', 'exists:products,id'],

            // ── Variants (at least one required) ──
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'variants.*.purchase_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'variants.*.discount_type' => ['nullable', 'in:percentage,fixed'],
            'variants.*.discount_value' => [
                'nullable',
                'required_with:variants.*.discount_type',
                'numeric',
                'min:0',
            ],
            'variants.*.stock_quantity' => ['required', 'integer', 'min:0'],
            'variants.*.low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'variants.*.weight' => ['nullable', 'numeric', 'min:0'],
            'variants.*.weight_unit' => ['nullable', 'required_with:variants.*.weight', 'in:kg,g,lb,oz'],
            'variants.*.thumbnail' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'variants.*.images' => ['nullable', 'array', 'max:10'],
            'variants.*.images.*' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'variants.*.is_active' => ['nullable'],
            'variants.*.is_default' => ['nullable'],

            // Each variant's set of option pairs, e.g. [{option: "Color", value: "Red"}, {option: "Size", value: "M"}]
            'variants.*.option_values' => ['nullable', 'array'],
            'variants.*.option_values.*.option' => ['required_with:variants.*.option_values', 'string', 'max:100'],
            'variants.*.option_values.*.value' => ['required_with:variants.*.option_values', 'string', 'max:100'],
            'variants.*.option_values.*.swatch' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'category_id.required' => 'Category is required.',
            'category_id.exists' => 'Selected category does not exist.',
            'brand_id.exists' => 'Selected brand does not exist.',
            'thumbnail.image' => 'Thumbnail must be an image.',
            'thumbnail.mimes' => 'Thumbnail must be jpeg, jpg, png or webp.',
            'thumbnail.max' => 'Thumbnail size cannot exceed 2MB.',
            'images.max' => 'You can upload a maximum of 10 images.',
            'is_active.required' => 'Status is required.',
            'related_products.*.exists' => 'One or more related products do not exist.',
            'variants.required' => 'Add at least one variant (a simple product needs exactly one).',
            'variants.min' => 'Add at least one variant (a simple product needs exactly one).',
            'variants.*.price.required' => 'Price is required for every variant.',
            'variants.*.stock_quantity.required' => 'Stock quantity is required for every variant.',
            'variants.*.discount_value.required_with' => 'Discount value is required when a discount type is selected.',
            'variants.*.weight_unit.required_with' => 'Weight unit is required when weight is provided.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $variants = $this->input('variants', []);

        foreach ($variants as $i => $variant) {
            $variants[$i]['low_stock_threshold'] = $variant['low_stock_threshold'] ?? 5;
            $variants[$i]['discount_type'] = $variant['discount_type'] ?? null;
            $variants[$i]['discount_value'] = $variant['discount_value'] ?? null;
        }

        $this->merge([
            'brand_id' => $this->input('brand_id') ?: null,
            'variants' => $variants,
        ]);
    }
}
