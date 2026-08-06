<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'location_ar',
        'location_en',
        'start_datetime',
        'end_datetime',
        'max_attendees',
        'status',
        'logo',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function speakers()
    {
        return $this->belongsToMany(Speaker::class, 'event_schedule_speaker');
    }
}
