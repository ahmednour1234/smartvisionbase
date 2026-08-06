<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSeoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

   // StoreSeoRequest & UpdateSeoRequest

public function rules(): array
{
    return [
        'title.ar' => 'nullable|string|max:255',
        'title.en' => 'nullable|string|max:255',
        'description.ar' => 'nullable|string|max:1000',
        'description.en' => 'nullable|string|max:1000',
        'keywords.ar' => 'nullable|string',
        'keywords.en' => 'nullable|string',
        'og_title.ar' => 'nullable|string|max:255',
        'og_title.en' => 'nullable|string|max:255',
        'og_description.ar' => 'nullable|string|max:1000',
        'og_description.en' => 'nullable|string|max:1000',
        'og_image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        'model_type' => 'nullable|string|max:100',
        'model_id' => 'nullable|integer',
    ];
}

}
