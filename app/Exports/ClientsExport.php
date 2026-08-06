<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Client::select([
            'name',
            'email',
                        'country_code',
            'phone',
            'job',
            'active',
            'form_id',
            'created_at',
            'updated_at'
        ])->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Country Code',
            'Phone',
            'Job',
            'Active',
            'Code',
            'Form ID',
            'Created At',
            'Updated At',
        ];
    }
}
