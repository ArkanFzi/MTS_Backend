<?php

namespace Modules\Admin\F10_CategoryMaster\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Tentukan apakah user berhak mengakses ini
     */
    public function authorize(): bool
    {
        // Pastikan user memiliki role admin
        return $this->user() && $this->user()->hasRole('admin');
    }

    /**
     * Aturan validasi
     */
    public function rules(): array
    {
        return [
            'name'        => 'required|string|min:3|max:100|unique:categories,name',
            'slug'        => 'nullable|string|unique:categories,slug',
            'description' => 'nullable|string|max:500',
            'parent_id'   => 'nullable|uuid|exists:categories,id', // Ganti 'string' ke 'uuid' jika ID kamu UUID
        ];
    }
}