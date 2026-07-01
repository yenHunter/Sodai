<?php

namespace App\Http\Requests\Admin\Category;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')->id;

        return [
            'name'        => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'parent_id'   => [
                'nullable',
                'integer',
                Rule::notIn([$categoryId]),
                'exists:categories,id',
                function ($attribute, $value, $fail) use ($categoryId) {
                    if ($value) {
                        $parent = Category::find($value);
                        if ($parent && $parent->isChild()) {
                            $fail('Sub-categories cannot be nested. Max 2 levels allowed.');
                        }
                        $category = Category::find($categoryId);
                        if ($category) {
                            $childIds = $category->children()->pluck('id')->toArray();
                            if (in_array($value, $childIds)) {
                                $fail('Cannot set a child category as parent.');
                            }
                        }
                    }
                },
            ],
            'image'       => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048',
            ],
            // ✅ Accept string 'active'/'inactive'
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
            'name.required'      => 'Category name is required.',
            'name.unique'        => 'This category name already exists.',
            'parent_id.not_in'   => 'A category cannot be its own parent.',
            'parent_id.exists'   => 'Selected parent category does not exist.',
            'image.image'        => 'File must be an image.',
            'image.mimes'        => 'Image must be jpeg, jpg, png or webp.',
            'image.max'          => 'Image size cannot exceed 2MB.',
            'is_active.required' => 'Status is required.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sort_order' => $this->input('sort_order') ?? 0,
            'parent_id'  => $this->input('parent_id') ?: null,
        ]);
    }
}