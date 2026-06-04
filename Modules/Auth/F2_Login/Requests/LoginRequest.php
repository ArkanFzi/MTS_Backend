<?php

namespace Modules\Auth\F2_Login\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
{
    return [
        'email'    => 'required|email', // <-- Ganti jadi email dan validasi format email
        'password' => 'required|string',
    ];
}

public function messages(): array
{
    return [
        'email.required'    => 'Email wajib diisi.',
        'email.email'       => 'Format email tidak valid.',
        'password.required' => 'Password wajib diisi.',
    ];
}
}