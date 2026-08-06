<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';
    public $timestamps = false;

    protected $fillable = ['name','event_date_from','event_date_to','location','is_active','sort_order','created_at'];
}
