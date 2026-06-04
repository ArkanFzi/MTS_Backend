<?php

namespace Modules\Admin\F8_CategoryTagManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('name') && !$this->has('slug')) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|unique:tags,name|max:30',
            'slug'  => 'required|string|unique:tags,slug|max:40',
            'color' => 'nullable|string|max:7', // untuk hex color code, contoh: #FF5733
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama tag wajib diisi.',
            'name.unique'   => 'Nama tag sudah terdaftar.',
            'slug.unique'   => 'Slug tag sudah digunakan.',
        ];
    }
}