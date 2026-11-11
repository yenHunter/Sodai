<?php

namespace App\Http\Requests\Visitor;

use App\Rules\ReCaptcha;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'g-recaptcha-response' => [
                'required',
                new ReCaptcha(
                    threshold: config('services.recaptcha.threshold', 0.5),
                    action: 'customer_contact',
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'phone.required' => 'Phone number is required.',
            'message.required' => 'Please enter your comments or questions.',
            'message.min' => 'Please provide a little more detail (at least 10 characters).',
            'message.max' => 'Your message is too long (maximum 5000 characters).',
            'g-recaptcha-response.required' => 'reCAPTCHA verification failed. Please try again.',
        ];
    }
}
