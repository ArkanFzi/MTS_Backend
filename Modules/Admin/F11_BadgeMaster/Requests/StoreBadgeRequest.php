<?php

namespace Modules\Admin\F11_BadgeMaster\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBadgeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:100|unique:badges,name',
            'description'     => 'required|string|max:255',
            'icon_url'        => 'required|string|max:255|url',
            'tier'            => 'required|in:bronze,silver,gold,platinum,diamond',
            'condition_type'  => 'required|string|max:50',
            'condition_value' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'tier.in' => 'Tier badge harus salah satu dari: bronze, silver, gold, platinum, diamond.',
        ];
    }
}