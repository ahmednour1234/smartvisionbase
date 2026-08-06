<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'title_ar',
        'title_en',
        'company_name_ar',
        'company_name_en',
        'phone',
        'image',
        'active',
        'category_sponsor_id',
    ];

    // علاقة BelongsTo مع تصنيف الراعي
    public function category()
    {
        return $this->belongsTo(SponsorCategory::class, 'category_sponsor_id');
    }
}
