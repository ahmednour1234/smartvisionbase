<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
        use SoftDeletes;

    protected $fillable = [
    'name',
    'start_date',
    'end_date',
    'bank_details',
    'floor_plan',
];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }
    public function companies()
{
    return $this->belongsToMany(Company::class, 'company_event')
        ->withTimestamps();
}
}
