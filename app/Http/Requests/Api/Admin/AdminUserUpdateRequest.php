<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminUserUpdateRequest extends FormRequest
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
        $id = (int) $this->route('id');
        return [
            'name' => 'sometimes|required|string|max:120',
            'email' => 'sometimes|required|email|max:160|unique:users,email,' . $id,
            'password' => 'sometimes|nullable|string|min:8|max:1024',
            'role' => 'sometimes|required|in:admin,staff',
            'is_active' => 'sometimes|boolean',
            'must_change_password' => 'sometimes|boolean',
        ];
    }
}
