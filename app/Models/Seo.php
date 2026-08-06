<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    protected $fillable = [
        'route',
        'title',
        'description',
        'keywords',
        'og_title',
        'og_description',
        'og_image',
    ];

    protected $casts = [
        'title'            => 'array',
        'description'      => 'array',
        'keywords'         => 'array',
        'og_title'         => 'array',
        'og_description'   => 'array',
    ];
}
