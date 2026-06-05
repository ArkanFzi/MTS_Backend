<?php

namespace Modules\Admin\F10_CategoryMaster\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

   public function rules(): array
{
    // Mengambil UUID dari URL (nama parameternya harus 'category')
    $categoryId = $this->route('category');

    return [
        // Tambahkan ',id' setelah $categoryId agar Laravel tahu kolomnya adalah 'id'
        'name'        => 'sometimes|string|min:3|max:100|unique:categories,name,' . $categoryId . ',id',
        'slug'        => 'sometimes|string|unique:categories,slug,' . $categoryId . ',id',
        'description' => 'nullable|string|max:500',
        'parent_id'   => 'nullable|uuid|exists:categories,id',
    ];
}
}