<?php

namespace App\Http\Requests\Sponsor;

use Illuminate\Foundation\Http\FormRequest;

class SponsorRegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name_en'   => ['required','string','max:255'],
            'email'     => ['required','email','unique:sponsors,email'],
            'password'  => ['required','string','min:8'],
            'title_en'  => ['nullable','string','max:255'],
            'company_name_en' => ['nullable','string','max:255'],
            'image'     => ['nullable','string','max:2048'],
            'country_code' => ['nullable','string','max:5'],
            // أضف حقول عربية إذا لزم
        ];
    }
}
