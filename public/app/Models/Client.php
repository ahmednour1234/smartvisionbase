<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasFcmToken;

class Client extends Authenticatable
{
use HasApiTokens, HasFactory, Notifiable,HasFcmToken;

    protected $fillable = [
        'name', 'email', 'phone', 'job', 'active', 'code', 'img', 'form_id','country_code','company_name','type','status','do_you_have_experince','section','category','email_verified_at',
        'verify_code_hash', 'verify_code_expires_at',
        'reset_code_hash', 'reset_code_expires_at','password','remember_toke','fcm_token'
    ];
    protected $hidden = ['password', 'verify_code_hash', 'reset_code_hash'];

    protected $casts = [
        'email_verified_at'       => 'datetime',
        'verify_code_expires_at'  => 'datetime',
        'reset_code_expires_at'   => 'datetime',
    ];
    public function form()
    {
        return $this->belongsTo(Form::class);
    }
    // app/Models/Registration.php
public function marketingRef() { return $this->belongsTo(\App\Models\MarketingRef::class); }

}
