<?php
// app/Exports/InvitationsTemplateExport.php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvitationsTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        // Example rows (optional). Keep empty array to deliver only headings.
        return [
            // ['John Doe','A-0001','VIP','present'],
        ];
    }

    public function headings(): array
    {
        return ['name','invitation_number','type','attendance'];
    }
}
