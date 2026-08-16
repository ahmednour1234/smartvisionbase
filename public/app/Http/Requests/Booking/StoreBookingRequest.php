<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // عدّلها حسب سياساتك
    }

    public function rules(): array
    {
        return [
            'sponsor_id' => ['required','exists:sponsors,id'],
            'time_from'  => ['required','date_format:Y-m-d H:i:s'],
            'time_to'    => ['required','date_format:Y-m-d H:i:s','after:time_from'],
            // اختياري: 'speaker_id' لو عندك
            'speaker_id' => ['nullable','exists:speakers,id'],
        ];
    }
}
