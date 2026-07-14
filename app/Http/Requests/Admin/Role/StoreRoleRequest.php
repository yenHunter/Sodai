<?php

namespace App\Http\Requests\Admin\Role;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9\-]+$/',
                'unique:roles,name,NULL,id,guard_name,admin',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Role name is required.',
            'name.regex'    => 'Role name may only contain lowercase letters, numbers and hyphens (e.g. order-manager).',
            'name.unique'   => 'This role name already exists.',
        ];
    }
}