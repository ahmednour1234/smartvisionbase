<?php
// app/Models/Booking.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'speaker_id','client_id','speaker_time_id','time_from','time_to','status','active','sponsor_id'
    ];

  protected $casts = [
    'date'   => 'date:Y-m-d',
    'active' => 'boolean',
    ];

    public function speaker()
    {
        return $this->belongsTo(Speaker::class);
    }
       public function sponsor()
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function speakerTime()
    {
        return $this->belongsTo(SpeakerTime::class);
    }

    public function scopeActive($q) { return $q->where('active', true); }
    public function scopeForSpeaker($q, $speakerId) { return $q->where('speaker_id', $speakerId); }
}
