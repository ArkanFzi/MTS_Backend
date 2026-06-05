<?php

namespace Modules\User\F16_Post\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Pastikan user sudah login melalui middleware di route
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|uuid|exists:categories,id',
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            'tags'        => 'nullable|array',
            'tags.*'      => 'exists:tags,id', // Memastikan tag yang dikirim ada di database
        ];
    }
}