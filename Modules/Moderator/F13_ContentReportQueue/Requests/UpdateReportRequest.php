<?php

namespace Modules\Moderator\F13_ContentReportQueue\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:reviewed,rejected,resolved',
            'reason' => 'nullable|string|max:255',
            'notes'  => 'nullable|string',
        ];
    }
}