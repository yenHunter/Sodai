<?php

namespace App\Http\Requests\Admin\Faq;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'faq_category_id' => ['required', 'integer', 'exists:faq_categories,id'],
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string', 'max:5000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'faq_category_id.required' => 'Please select a category.',
            'faq_category_id.exists' => 'Selected category does not exist.',
            'question.required' => 'Question is required.',
            'answer.required' => 'Answer is required.',
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
