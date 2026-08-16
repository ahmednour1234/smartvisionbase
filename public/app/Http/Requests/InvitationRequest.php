<?php

// app/Http/Requests/InvitationRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvitationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('invitation')?->id;
        return [
            'name'               => ['required','string','max:150'],
            'invitation_number'  => ['required','string','max:50','unique:invitations,invitation_number,'.($id ?? 'NULL').',id'],
            'type'               => ['nullable','string','max:50'],
            'attendance'         => ['nullable','boolean'],
        ];
    }
}
