<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Speaker extends Model
{
    use HasFactory;
    protected $fillable = [
    'name_ar', 'name_en',
    'title_ar', 'title_en',
    'company_name_ar', 'company_name_en',
    'linkedin', 'social_links', 'image','youtube','facebook','tiktok'
];
    public function schedules()
    {
        return $this->belongsToMany(EventSchedule::class, 'event_schedule_speaker');
    }
    
}
