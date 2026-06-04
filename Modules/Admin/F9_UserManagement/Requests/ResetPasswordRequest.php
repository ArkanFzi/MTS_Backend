<?php

namespace Modules\Admin\F9_UserManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return ['password' => 'required|string|min:8|confirmed'];
    }
}