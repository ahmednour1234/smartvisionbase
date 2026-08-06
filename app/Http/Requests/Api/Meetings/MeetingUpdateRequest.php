<?php

namespace App\Http\Requests\Api\Meetings;

use Illuminate\Foundation\Http\FormRequest;

class MeetingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'meeting_date' => 'sometimes|required|date',
            'duration_minutes' => 'sometimes|nullable|integer|min:0|max:1440',
            'meeting_type' => 'sometimes|nullable|in:call,online,in_person',
            'notes' => 'sometimes|nullable|string|max:255',
        ];
    }
}
