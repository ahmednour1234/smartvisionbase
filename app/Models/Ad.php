<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = [
        'name', 'description', 'img', 'start_date', 'end_date', 'active', 'sort', 'link', 'views'
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date'
    ];
}
