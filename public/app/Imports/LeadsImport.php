<?php
// app/Imports/LeadsImport.php
namespace App\Imports;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LeadsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $assignedUserId = null;
        if (!empty($row['assigned_user_email'])) {
            $u = User::where('email', trim((string)$row['assigned_user_email']))->first();
            $assignedUserId = $u?->id;
        }

        return new Lead([
            'name'  => $row['name'] ?? '',
            'email' => $row['email'] ?? null,
            'phone' => $row['phone'] ?? null,
            'job'   => $row['job'] ?? null,
            'assigned_user_id' => $assignedUserId,
            'source' => $row['source'] ?? null,
            'notes'  => $row['notes'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.name' => ['nullable','string','max:190'],
            '*.email'=> ['nullable','email','max:190'],
            '*.phone'=> ['nullable','max:50'],
            '*.job'  => ['nullable','string','max:190'],
        ];
    }
}
