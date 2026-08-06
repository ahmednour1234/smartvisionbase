<?php

namespace App\Http\Requests\Api\Meetings;

use Illuminate\Foundation\Http\FormRequest;

class MeetingStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'lead_id' => 'nullable|integer|exists:leads,id',
            'meeting_date' => 'required|date',
            'duration_minutes' => 'nullable|integer|min:0|max:1440',
            'meeting_type' => 'nullable|in:call,online,in_person',
            'notes' => 'nullable|string|max:255',
        ];
    }
}
