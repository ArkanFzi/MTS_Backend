<?php

namespace Modules\Auth\F31_ForgotPassword\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email'    => 'required|email',
            'token'    => 'required|string',
            'password' => 'required|min:8|confirmed',
        ];
    }
}