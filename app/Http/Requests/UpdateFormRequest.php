<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFormRequest extends FormRequest
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
            'number'      => 'nullable|integer',
            'img'         => 'nullable',
            'active'      => 'nullable|boolean',
        ];
    }
}
