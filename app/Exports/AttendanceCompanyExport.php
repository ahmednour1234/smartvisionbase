<?php
// app/Exports/AttendanceCompanyExport.php
namespace App\Exports;

use App\Models\AttendanceCompany;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AttendanceCompanyExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection()
    {
        return AttendanceCompany::query()
            ->select('id','name','active','attendance','attendance_at','created_at','updated_at','deleted_at')
            ->get()
            ->map(fn ($r) => [
                $r->id,
                $r->name,
                $r->active ? 1 : 0,
                $r->attendance ? 1 : 0,
                optional($r->attendance_at)->toDateTimeString(),
                optional($r->created_at)->toDateTimeString(),
                optional($r->updated_at)->toDateTimeString(),
                optional($r->deleted_at)->toDateTimeString(),
            ]);
    }

    public function headings(): array
    {
        return ['id','name','active','attendance','attendance_at','created_at','updated_at','deleted_at'];
    }
}
