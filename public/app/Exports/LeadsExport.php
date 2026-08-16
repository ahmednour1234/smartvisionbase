<?php
// app/Exports/LeadsExport.php
namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeadsExport implements FromQuery, WithHeadings
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $q = Lead::query();

        if ($s = trim((string)($this->filters['q'] ?? ''))) {
            $q->where(function($x) use ($s) {
                $x->where('name','like',"%{$s}%")
                  ->orWhere('email','like',"%{$s}%")
                  ->orWhere('phone','like',"%{$s}%")
                  ->orWhere('job','like',"%{$s}%");
            });
        }
        if (!empty($this->filters['status'])) {
            $q->where('status', $this->filters['status']);
        }
        if (!empty($this->filters['assigned_user_id'])) {
            $q->where('assigned_user_id', (int)$this->filters['assigned_user_id']);
        }
        if (!empty($this->filters['from'])) {
            $q->whereDate('updated_at', '>=', $this->filters['from']);
        }
        if (!empty($this->filters['to'])) {
            $q->whereDate('updated_at', '<=', $this->filters['to']);
        }

        return $q->select('id','name','email','phone','job','status','assigned_user_id','source','notes','updated_at');
    }

    public function headings(): array
    {
        return ['id','name','email','phone','job','status','assigned_user_id','source','notes','updated_at'];
    }
}
