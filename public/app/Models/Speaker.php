<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Concerns\HasFcmToken;

class Speaker extends Authenticatable
{
    use Notifiable  , HasApiTokens,HasFcmToken;

    protected $fillable = [
        'name_ar', 'name_en',
        'title_ar', 'title_en',
        'company_name_ar', 'company_name_en',
        'linkedin', 'social_links', 'image','youtube','facebook','tiktok',
        'type','number_of_followers','instgram','orders','country_code',
        'email','password','vip','followers_ticktock','section'
    ];

    protected $hidden = ['password','remember_token'];

    public function schedules()
    {
        return $this->belongsToMany(EventSchedule::class, 'event_schedule_speaker');
    }

    public function getCountryNameAttribute()
    {
        $countries = cache()->get('countries.v1.data', []);
        $rec = collect($countries)->firstWhere('code', $this->country_code);
        return $rec['name_ar'] ?? $rec['name_en'] ?? null;
    }

    public function getCountryFlagAttribute()
    {
        $countries = cache()->get('countries.v1.data', []);
        $rec = collect($countries)->firstWhere('code', $this->country_code);
        return $rec['flag_svg'] ?? $rec['flag_png'] ?? null;
    }
}

