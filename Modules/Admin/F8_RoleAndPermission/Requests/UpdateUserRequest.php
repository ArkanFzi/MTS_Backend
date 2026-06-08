<?php

namespace Modules\Admin\F8_RoleAndPermission\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'role' => 'required|string|in:moderator,user,admin', 
        ];
    }
}