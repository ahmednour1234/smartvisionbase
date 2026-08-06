<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'sometimes|required|array',
            'name.ar'     => 'sometimes|required|string|max:255',
            'name.en'     => 'sometimes|required|string|max:255',
            'description' => 'nullable|array',
            'description.ar' => 'nullable|string',
            'description.en' => 'nullable|string',
            'img'         => 'nullable',
            'start_date'  => 'sometimes|required|date',
            'end_date'    => 'sometimes|required|date|after_or_equal:start_date',
            'active'      => 'sometimes|required|boolean',
            'sort'        => 'nullable|integer',
            'link'        => 'nullable|url',
        ];
    }
}
