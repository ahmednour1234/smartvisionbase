<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    // ننظّف ونوحّد الإيميل قبل التحقق
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim((string) $this->input('email'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'name'         => ['required','string','max:255'],
            'email'        => [
                'required',
                'string',
                'email:rfc',
                'max:255',
                // فريد داخل clients (مع تجاهل المحذوفين لو عندك soft deletes)
                Rule::unique('clients', 'email')->whereNull('deleted_at'),
                // وفريد أيضاً داخل sponsors
                Rule::unique('sponsors', 'email')->whereNull('deleted_at'),
            ],
            'phone'        => ['required','string','max:30'],
            'password'     => ['required','string','min:6','confirmed'],
            'job'          => ['nullable','string','max:255'],
            'fcm_token'    => ['nullable','string'],
            'country_code' => ['required','string','max:5'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل.',
        ];
    }
}
 