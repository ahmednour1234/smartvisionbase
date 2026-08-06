<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    protected $fillable = [
        'title',
        'description',
        'media_type',
        'media_path',
        'section_order',
        'is_active',
        'thumbnail'
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'is_active' => 'boolean',
    ];
}
