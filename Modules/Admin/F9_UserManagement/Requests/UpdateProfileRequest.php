<?php

namespace Modules\Admin\F9_UserManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function rules(): array
    {
        $userId = $this->route('id');
        return [
            'username'   => 'sometimes|string|max:50|unique:users,username,' . $userId,
            'email'      => 'sometimes|email|max:255|unique:users,email,' . $userId,
            'avatar_url' => 'nullable|url',
            'bio'        => 'nullable|string|max:500',
        ];
    }
}