<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pixel extends Model
{
    protected $fillable = [
        'name',
        'pixel_id',
        'active',
    ];
}
