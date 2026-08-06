<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMultiMediaRequest extends FormRequest
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
            'images'  => 'nullable|array',
            'images.*'=> 'nullable', // صور فقط وحجم أقصى 2 ميغابايت
            'links'   => 'nullable|array',
            'links.*' => 'nullable|url',
            'date'    => 'nullable|date',
            'active'  => 'required|boolean',
            'multi_media_category_id' => 'required|exists:multimedia_categories,id',
        ];
    }
}
