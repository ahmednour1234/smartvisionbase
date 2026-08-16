<?php


// app/Exports/InvitationsExport.php
namespace App\Exports;

use App\Models\Invitation;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvitationsExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        protected ?string $search = null,
        protected ?string $type = null,
        protected ?string $attendance = null // 'all' | 'present' | 'absent'
    ) {}

    public function query()
    {
        $q = Invitation::query();

        if ($this->search) {
            $q->where(function ($w) {
                $w->where('name', 'like', "%{$this->search}%")
                  ->orWhere('invitation_number', 'like', "%{$this->search}%");
            });
        }

        if ($this->type) {
            $q->where('type', $this->type);
        }

        if ($this->attendance === 'present') $q->where('attendance', true);
        if ($this->attendance === 'absent')  $q->where('attendance', false);

        return $q->orderBy('id');
    }

    public function headings(): array
    {
        return ['name','invitation_number','type','attendance'];
    }

    public function map($inv): array
    {
        return [
            $inv->name,
            $inv->invitation_number,
            $inv->type,
            $inv->attendance ? 'present' : 'absent',
        ];
    }
}
