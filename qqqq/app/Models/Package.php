<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $guarded = [];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
