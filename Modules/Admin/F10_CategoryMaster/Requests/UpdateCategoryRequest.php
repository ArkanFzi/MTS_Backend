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
        return [
            'name' => 'sometimes|string|min:3|max:100|unique:categories,name,' . $this->route('id'),
            'slug' => 'sometimes|string|unique:categories,slug,' . $this->route('id'),
            'description' => 'nullable|string|max:500',
            'parent_id' => 'nullable|string|exists:categories,id',
        ];
    }
}