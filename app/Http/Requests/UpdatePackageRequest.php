<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackageRequest extends StorePackageRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['image'] = 'nullable|image|mimes:jpg,jpeg,png';
        return $rules;
    }
}
