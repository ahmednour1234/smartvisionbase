<?php
// app/Imports/InvitationsImport.php
namespace App\Imports;

use App\Models\Invitation;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Throwable;

class InvitationsImport implements ToModel, WithHeadingRow, SkipsOnError
{
    public function model(array $row)
    {
        // Normalize keys (heading row: name, invitation_number, type, attendance)
        $attendance = $this->toBool($row['attendance'] ?? null);

        return Invitation::updateOrCreate(
            ['invitation_number' => (string)($row['invitation_number'] ?? '')],
            [
                'name'       => (string)($row['name'] ?? ''),
                'type'       => $row['type'] ?? null,
                'attendance' => $attendance,
            ]
        );
    }

    protected function toBool($v): bool
    {
        if (is_bool($v)) return $v;
        $t = Str::lower(trim((string)$v));
        return in_array($t, ['1','true','yes','y','present','attended','حاضر','نعم','✓'], true);
    }

    public function onError(Throwable $e) { /* skip row */ }
}
