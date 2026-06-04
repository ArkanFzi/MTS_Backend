<?php

namespace Modules\Admin\F12_TagMaster\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:100|unique:tags,name',
            'color' => 'nullable|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Nama tag sudah digunakan.',
            'color.regex' => 'Format warna harus berupa kode hex (contoh: #3b82f6)',
        ];
    }
}