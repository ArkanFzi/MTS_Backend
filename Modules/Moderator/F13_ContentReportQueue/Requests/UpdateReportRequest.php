<?php

namespace Modules\Moderator\F13_ContentReportQueue\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->hasRole('moderator') || auth()->user()->hasRole('admin'));
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:reviewed,rejected,resolved',
            'action' => 'required_if:status,resolved|in:warn,ban,none',
            'reason' => 'nullable|string|max:255',
            'notes'  => 'nullable|string',
        ];
    }
}