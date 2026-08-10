<?php
// app/Models/Qrcode.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Qrcode extends Model
{
    protected $fillable = [
        'qrcode',
        'active',
        'short_code',
        'email',
        'scan',
        'register_id',
    ];

    public function registration()
    {
        return $this->belongsTo(Client::class, 'register_id');
    }
}
