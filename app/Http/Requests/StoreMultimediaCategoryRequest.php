<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMultimediaCategoryRequest extends FormRequest
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
            'logo'    => 'nullable',
            'promo'    => 'nullable|url',
            'active'  => 'boolean',
            'description_ar'=>'nullable',
            'description_en'=>'nullable'
        ];
    }
}
