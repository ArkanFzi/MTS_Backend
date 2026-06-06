<?php

namespace Modules\Admin\F11_BadgeMaster\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBadgeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:100|unique:badges,name,' . $this->route('badge'),
            'description'     => 'required|string|max:255',
            'icon_url'        => 'required|string|max:255|url',
            'tier'            => 'required|in:bronze,silver,gold,platinum,diamond',
            'condition_type'  => 'required|in:reputation_points,post_count,comment_count',
            'condition_value' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'condition_type.in' => 'Tipe kondisi tidak valid. Gunakan: reputation_points, post_count, atau comment_count.',
        ];
    }
}