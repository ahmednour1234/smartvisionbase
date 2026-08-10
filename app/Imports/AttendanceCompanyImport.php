<?php
// app/Imports/AttendanceCompanyImport.php
namespace App\Imports;

use App\Models\AttendanceCompany;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class AttendanceCompanyImport implements ToModel, WithHeadingRow, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        return new AttendanceCompany([
            'name'           => (string)($row['name'] ?? ''),
            'active'         => isset($row['active']) ? (bool)$row['active'] : true,
            'attendance'     => isset($row['attendance']) ? (bool)$row['attendance'] : false,
            'attendance_at'  => !empty($row['attendance_at']) ? Carbon::parse($row['attendance_at']) : null,
        ]);
    }
}
