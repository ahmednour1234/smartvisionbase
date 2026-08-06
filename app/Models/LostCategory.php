<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LostCategory extends Model
{
    protected $table = 'lost_categories';
    public $timestamps = false;

    protected $fillable = ['name','is_active','sort_order','created_at'];
}
