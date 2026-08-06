<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientsExport implements FromCollection, WithHeadings
{
    protected $form_id;
    protected $date_from;
    protected $date_to;

    public function __construct($form_id = null, $date_from = null, $date_to = null)
    {
        $this->form_id = $form_id;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
    }

    public function collection()
    {
        $query = Client::select([
            'name',
            'email',
            'country_code',
            'phone',
            'job',
            'active',
            'form_id',
            'created_at',
            'updated_at'
        ]);

        if ($this->form_id) {
            $query->where('form_id', $this->form_id);
        }

        if ($this->date_from) {
            $query->whereDate('created_at', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->whereDate('created_at', '<=', $this->date_to);
        }

        return $query->get();
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
            'Form ID',
            'Created At',
            'Updated At',
        ];
    }
}
