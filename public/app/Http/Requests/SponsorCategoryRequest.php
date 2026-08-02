<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SponsorCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'active' => 'nullable|boolean',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png',
        ];
    }
}
