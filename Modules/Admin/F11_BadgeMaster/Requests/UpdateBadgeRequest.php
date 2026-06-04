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
            'condition_type'  => 'required|string|max:50',
            'condition_value' => 'required|integer|min:1',
        ];
    }
}