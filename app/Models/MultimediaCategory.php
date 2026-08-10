<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MultimediaCategory extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'logo',
        'promo', // Assuming promo is a URL or a string
        'active',
        'description_ar',
        'description_en'
    ];
}
