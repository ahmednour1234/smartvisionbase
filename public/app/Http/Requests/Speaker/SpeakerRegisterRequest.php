<?php

namespace App\Http\Requests\Speaker;

use Illuminate\Foundation\Http\FormRequest;

class SpeakerRegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name_ar'  => ['nullable','string','max:255'],
            'name_en'  => ['required','string','max:255'],
            'email'    => ['required','email','max:255','unique:speakers,email'],
            'password' => ['required','string','min:6','max:100'],
            'country_code' => ['nullable','string','max:5'],
        ];
    }
}
