<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'title_ar',
        'title_en',
        'img',
        'active',
        'description_ar',
        'description_en',
        'order'
    ];
}
