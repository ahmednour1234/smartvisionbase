<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'event_date' => 'required|date',
            'address_ar' => 'required|string|max:255',
            'address_en' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'attendees_limit' => 'nullable|integer|min:0',
            'main_image' => 'nullable|image|mimes:jpg,jpeg,png',
            'active' => 'boolean',
        ];
    }
}
