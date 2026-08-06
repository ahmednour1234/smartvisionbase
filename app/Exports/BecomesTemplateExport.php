<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BecomesTemplateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return collect([
            ['name' => 'أحمد محمد', 'email' => 'ahmed@example.com', 'phone' => '01123456789', 'job' => 'محاسب','country_code'=>'+20','company_name'=>'Smart Vision'],
            ['name' => 'منى علي', 'email' => 'mona@example.com', 'phone' => '01012345678', 'job' => 'مديرة تسويق','country_code'=>'+20','company_name'=>'Smart Vision'],
        ]);
    }

    public function headings(): array
    {
        return [
            'name',
            'email',
            'phone',
            'job',
          'country_code'
          ];
    }
}
