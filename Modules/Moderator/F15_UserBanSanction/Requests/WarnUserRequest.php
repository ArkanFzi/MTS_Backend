<?php

namespace Modules\Moderator\F15_UserBanSanction\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarnUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => 'required|string|max:500',
            'notes'  => 'nullable|string|max:1000',
        ];
    }
}