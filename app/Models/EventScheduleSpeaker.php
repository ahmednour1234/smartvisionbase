<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventScheduleSpeaker extends Model
{
    protected $table = 'event_schedule_speaker';

    protected $fillable = [
        'event_id',
        'speaker_id',
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
