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
            'name'                 => [
                'required',
                'string',
                'max:255',
            ],
            // ❌ SKU removed — auto-generated in ProductService
            'short_description'    => [
                'nullable',
                'string',
                'max:500',
            ],
            'description'          => [
                'nullable',
                'string',
            ],

            // ── Relationships ──
            'category_id'          => [
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
            'brand_id'             => [
                'nullable',
                'integer',
                'exists:brands,id',
            ],

            // ── Pricing ──
            'price'                => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],
            'purchase_price'       => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],
            'discount_type'        => [
                'nullable',
                'in:percentage,fixed',
            ],
            'discount_value'       => [
                'nullable',
                'required_with:discount_type',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($this->input('discount_type') === 'percentage' && $value > 100) {
                        $fail('Percentage discount cannot exceed 100.');
                    }
                    if ($this->input('discount_type') === 'fixed' && $value > $this->input('price')) {
                        $fail('Fixed discount cannot exceed the product price.');
                    }
                },
            ],

            // ── Stock ──
            'stock_quantity'       => [
                'required',
                'integer',
                'min:0',
            ],
            'low_stock_threshold'  => [
                'nullable',
                'integer',
                'min:0',
            ],

            // ── Media ──
            'thumbnail'            => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048',
            ],
            'images'               => [
                'nullable',
                'array',
                'max:10',
            ],
            'images.*'             => [
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048',
            ],

            // ── Physical Attributes ──
            'weight'               => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'weight_unit'          => [
                'nullable',
                'required_with:weight',
                'in:kg,g,lb,oz',
            ],
            'color'                => [
                'nullable',
                'string',
                'max:50',
            ],
            'size'                 => [
                'nullable',
                'string',
                'max:50',
            ],

            // ── Status ──
            'is_active'            => [
                'required',
            ],
            'is_featured'          => [
                'nullable',
            ],

            // ── SEO Meta ──
            'meta'                 => [
                'nullable',
                'array',
            ],
            'meta.meta_title'      => [
                'nullable',
                'string',
                'max:255',
            ],
            'meta.meta_description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'meta.meta_keywords'   => [
                'nullable',
                'string',
                'max:255',
            ],

            // ── Tags ──
            'tags'                 => [
                'nullable',
                'array',
            ],
            'tags.*'               => [
                'string',
                'max:50',
            ],

            // ── Related Products ──
            'related_products'     => [
                'nullable',
                'array',
            ],
            'related_products.*'   => [
                'integer',
                'exists:products,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                => 'Product name is required.',
            'category_id.required'          => 'Category is required.',
            'category_id.exists'            => 'Selected category does not exist.',
            'brand_id.exists'               => 'Selected brand does not exist.',
            'price.required'                => 'Price is required.',
            'discount_value.required_with'  => 'Discount value is required when discount type is selected.',
            'stock_quantity.required'       => 'Stock quantity is required.',
            'thumbnail.image'               => 'Thumbnail must be an image.',
            'thumbnail.mimes'               => 'Thumbnail must be jpeg, jpg, png or webp.',
            'thumbnail.max'                 => 'Thumbnail size cannot exceed 2MB.',
            'images.max'                    => 'You can upload a maximum of 10 images.',
            'images.*.image'                => 'Each file must be an image.',
            'images.*.mimes'                => 'Images must be jpeg, jpg, png or webp.',
            'images.*.max'                  => 'Each image cannot exceed 2MB.',
            'weight_unit.required_with'     => 'Weight unit is required when weight is provided.',
            'is_active.required'            => 'Status is required.',
            'related_products.*.exists'     => 'One or more related products do not exist.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'low_stock_threshold' => $this->input('low_stock_threshold') ?? 5,
            'brand_id'            => $this->input('brand_id') ?: null,
            'discount_type'       => $this->input('discount_type') ?: null,
            'discount_value'      => $this->input('discount_value') ?: null,
        ]);
    }
}