<?php
// app/Models/SpeakerTime.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpeakerTime extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'speaker_id','date','time_from','time_to','status','active',
    ];

    protected $casts = [
        'date'      => 'date',
        'time_from' => 'datetime:H:i',
        'time_to'   => 'datetime:H:i',
        'active'    => 'boolean',
    ];

    // علاقات
    public function speaker()
    {
        return $this->belongsTo(Speaker::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // سكوبات سريعة
    public function scopeActive($q) { return $q->where('active', true); }
    public function scopeForSpeaker($q, $speakerId) { return $q->where('speaker_id', $speakerId); }
}
