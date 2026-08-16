<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Concerns\HasFcmToken;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sponsor extends Model
{
    use HasApiTokens, HasFactory,HasFcmToken,SoftDeletes;

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
        'orders',
        'link_profile',
                'email','password'
    ];
    protected $hidden = ['password','remember_token'];

    // علاقة BelongsTo مع تصنيف الراعي
    public function category()
    {
        return $this->belongsTo(SponsorCategory::class, 'category_sponsor_id');
    }
        protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
