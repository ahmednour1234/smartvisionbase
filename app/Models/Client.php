<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'job', 'active', 'code', 'img', 'form_id','country_code','company_name','type'
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
