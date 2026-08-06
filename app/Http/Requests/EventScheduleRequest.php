<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id'     => 'required|exists:events,id',
            'title_ar'     => 'required|string|max:255',
            'title_en'     => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'location_ar'  => 'nullable|string|max:255',
            'location_en'  => 'nullable|string|max:255',
            'max_attendees' => 'nullable|integer|min:1',
            'start_datetime'   => 'required|date',
            'end_datetime'     => 'required|date|after_or_equal:start_datetime',
            'logo'         => 'nullable',
            'speaker_ids'  => 'nullable|array',
            'speaker_ids.*'=> 'nullable|exists:speakers,id',
        ];
    }
}
