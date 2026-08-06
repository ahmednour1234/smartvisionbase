<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'sort' => 'nullable|integer',
            'active' => 'required|boolean',
            'duration' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
        ];
    }
}
