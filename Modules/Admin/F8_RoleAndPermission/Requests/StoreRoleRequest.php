<?php

namespace Modules\Admin\F8_RoleAndPermission\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin'); // Hanya admin yang boleh
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:50|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Role name already exists.',
        ];
    }
}