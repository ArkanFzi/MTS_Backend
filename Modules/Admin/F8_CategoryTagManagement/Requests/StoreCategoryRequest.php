<?php

namespace Modules\Admin\F8_CategoryTagManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Otomatis bikin slug dari name kalau slug-nya kosong
        if ($this->has('name') && !$this->has('slug')) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|unique:categories,name|max:50',
            'slug'        => 'required|string|unique:categories,slug|max:60',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|uuid|exists:categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Nama kategori wajib diisi.',
            'name.unique'      => 'Nama kategori sudah digunakan.',
            'slug.unique'      => 'Slug kategori sudah digunakan.',
            'parent_id.exists' => 'ID Parent kategori tidak valid.',
        ];
    }
}