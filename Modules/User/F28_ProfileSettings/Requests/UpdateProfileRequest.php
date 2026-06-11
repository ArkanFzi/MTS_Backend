<?php

namespace Modules\User\F28_ProfileSettings\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $user = auth()->user();
        return [
            'username' => 'sometimes|string|max:50|unique:users,username,' . $user->id,
            'email'    => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'avatar'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'bio'      => 'nullable|string|max:500',
        ];
    }
}