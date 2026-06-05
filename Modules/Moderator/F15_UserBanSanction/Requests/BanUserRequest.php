<?php

namespace Modules\Moderator\F15_UserBanSanction\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BanUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => 'required|string|max:255',
            'notes'  => 'nullable|string',
        ];
    }
}