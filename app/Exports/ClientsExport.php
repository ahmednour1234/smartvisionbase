<?php

namespace App\Exports;

use App\Models\Client;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClientsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $form_id;
    protected $date_from;
    protected $date_to;

    public function __construct($form_id = null, $date_from = null, $date_to = null)
    {
        $this->form_id   = $form_id;
        $this->date_from = $date_from;
        $this->date_to   = $date_to;
    }

    public function collection()
    {
        $query = Client::query()->select([
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

        // ✅ form_id: ما تعتمدش على truthy لأنه لو "0" هيبقى false
        if ($this->form_id !== null && $this->form_id !== '') {
            $query->where('form_id', $this->form_id);
        }

        // ✅ date_from
        if (!empty($this->date_from)) {
            $from = Carbon::parse($this->date_from)->startOfDay();
            $query->where('created_at', '>=', $from);
        }

        // ✅ date_to
        if (!empty($this->date_to)) {
            $to = Carbon::parse($this->date_to)->endOfDay();
            $query->where('created_at', '<=', $to);
        }

        return $query->latest()->get();
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

    // ✅ اختياري: يخلي Active يظهر Yes/No بدل 0/1
    public function map($row): array
    {
        return [
            $row->name,
            $row->email,
            $row->country_code,
            $row->phone,
            $row->job,
            $row->active ? 'Yes' : 'No',
            $row->form_id,
            optional($row->created_at)->format('Y-m-d H:i:s'),
            optional($row->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}
