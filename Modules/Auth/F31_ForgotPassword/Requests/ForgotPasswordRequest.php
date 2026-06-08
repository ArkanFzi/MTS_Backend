<?php

namespace Modules\Auth\F31_ForgotPassword\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return ['email' => 'required|email'];
    }
}