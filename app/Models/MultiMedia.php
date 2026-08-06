<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MultiMedia extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'images',
        'links',
        'date',
        'active',
        'multi_media_category_id',
    ];

    protected $casts = [
        'images' => 'array',
        'links' => 'array',
        'date' => 'date',
        'active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(MultimediaCategory::class, 'multi_media_category_id');
    }
}
