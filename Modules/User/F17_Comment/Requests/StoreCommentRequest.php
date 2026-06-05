<?php

namespace Modules\User\F17_Comment\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pastikan user sudah login untuk bisa berkomentar
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'body'      => 'required|string|max:5000',
            'parent_id' => 'nullable|uuid|exists:comments,id',
        ];
    }
}