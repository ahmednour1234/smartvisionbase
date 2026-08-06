<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminUserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        $u = $this->user();
        return $u && $u->role === 'admin';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => is_string($this->email) ? trim($this->email) : $this->email,
            'name' => is_string($this->name) ? trim($this->name) : $this->name,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:160|unique:users,email',
            'password' => 'required|string|min:8|max:1024',
            'role' => 'required|in:admin,staff',
            'is_active' => 'sometimes|boolean',
            'must_change_password' => 'sometimes|boolean',
        ];
    }
}
