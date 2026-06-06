<?php

namespace Modules\User\F30_UserReport\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
    return [
        'target_id'   => ['required', 'uuid'],
        'target_type' => ['required', Rule::in(['post', 'comment'])],
        'reason'      => ['required', 'string', 'max:100'],
        'description' => ['nullable', 'string'],
    ];
}
}