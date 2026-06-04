<?php

namespace Modules\Admin\F12_TagMaster\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:100|unique:tags,name,' . $this->route('tag'),
            'color' => 'nullable|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ];
    }
}