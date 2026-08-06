<?php
// app/Exports/AttendanceCompanyTemplateExport.php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AttendanceCompanyTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        // رؤوس الأعمدة المطلوبة أثناء الرفع
        return ['name', 'active', 'attendance', 'attendance_at'];
    }

    public function array(): array
    {
        // صف مثال (اختياري) لتوضيح القيم المتوقعة
        return [[
            'شركة مثال',
            1,                  // active: 1=مفعّل، 0=غير مفعّل
            0,                  // attendance: 1=حاضر، 0=غير حاضر
            '2025-10-01 09:00'  // attendance_at: تاريخ/وقت أو اتركه فاضي
        ]];
    }
}
