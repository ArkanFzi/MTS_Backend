<?php

namespace Modules\User\F22_VoteSystem\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'target_id'   => 'required|uuid',
            'target_type' => 'required|in:post,comment',
            'type'        => 'required|in:up,down',
        ];
    }
}