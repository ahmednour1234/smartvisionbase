<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMultimediaCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_ar' => 'sometimes|required|string|max:255',
            'name_en' => 'sometimes|required|string|max:255',
            'logo'    => 'nullable|image|mimes:jpg,jpeg,png',
            'promo'    => 'nullable|url',
            'active'  => 'boolean',
                        'description_ar'=>'nullable',
            'description_en'=>'nullable'
        ];
    }
}
