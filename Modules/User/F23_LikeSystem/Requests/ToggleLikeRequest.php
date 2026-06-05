<?php

namespace Modules\User\F23_LikeSystem\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ToggleLikeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'target_id'   => 'required|uuid',
            'target_type' => 'required|in:post,comment',
        ];
    }
}