<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|array',
            'name.ar'     => 'required|string|max:255',
            'name.en'     => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.ar' => 'nullable|string',
            'description.en' => 'nullable|string',
            'img'         => 'nullable',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'active'      => 'required|boolean',
            'sort'        => 'nullable|integer',
            'link'        => 'nullable|url',
        ];
    }
}
