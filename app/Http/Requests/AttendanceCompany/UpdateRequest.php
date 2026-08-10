<?php
// app/Http/Requests/AttendanceCompany/UpdateRequest.php
namespace App\Http\Requests\AttendanceCompany;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'           => ['required','string','max:255'],
            'active'         => ['nullable','boolean'],
            'attendance'     => ['nullable','boolean'],
            'attendance_at'  => ['nullable','date'],
        ];
    }
}
