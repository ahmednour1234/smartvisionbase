<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AuthChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required|string|max:1024',
            'new_password' => 'required|string|min:8|max:1024|confirmed',
        ];
    }
}
