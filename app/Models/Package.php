<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = 'packages';
    public $timestamps = false;

    protected $fillable = ['name','is_active','sort_order','created_at'];
}
