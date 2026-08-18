<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateMarketingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords'    => ['nullable', 'string', 'max:255'],
            'og_image'         => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'remove_og_image'  => ['nullable'],

            'facebook_url'  => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'twitter_url'   => ['nullable', 'url', 'max:255'],
            'youtube_url'   => ['nullable', 'url', 'max:255'],
            'linkedin_url'  => ['nullable', 'url', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'facebook_url.url'  => 'Please enter a valid Facebook URL.',
            'instagram_url.url' => 'Please enter a valid Instagram URL.',
            'twitter_url.url'   => 'Please enter a valid Twitter/X URL.',
            'youtube_url.url'   => 'Please enter a valid YouTube URL.',
            'linkedin_url.url'  => 'Please enter a valid LinkedIn URL.',
        ];
    }
}