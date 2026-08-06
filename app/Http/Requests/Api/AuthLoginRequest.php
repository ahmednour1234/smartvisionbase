<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AuthLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => is_string($this->email) ? trim($this->email) : $this->email,
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|max:160',
            'password' => 'required|string|max:1024',
        ];
    }
}
