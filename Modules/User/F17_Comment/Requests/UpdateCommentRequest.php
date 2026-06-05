<?php

namespace Modules\User\F17_Comment\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pengecekan kepemilikan dilakukan di Service, 
        // di sini cukup pastikan user terautentikasi
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'body' => 'required|string|max:5000',
        ];
    }
}