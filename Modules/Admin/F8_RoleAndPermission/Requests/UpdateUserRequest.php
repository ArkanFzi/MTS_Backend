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
            'username' => 'sometimes|string|min:3|max:100|unique:users,username,' . $this->route('id'),
            'email' => 'sometimes|email|unique:users,email,' . $this->route('id'),
            'avatar_url' => 'nullable|string|url',
            'bio' => 'nullable|string|max:500',
            'is_banned' => 'nullable|boolean',
            'reputation_points' => 'nullable|integer|min:0',
        ];
    }
}