<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormRequest extends FormRequest
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
            'number'      => 'nullable|integer',
            'img'         => 'nullable',
            'active'      => 'nullable|boolean',
        ];
    }
}
