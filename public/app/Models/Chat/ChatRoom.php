<?php

namespace App\Models\Chat;

use App\Models\EventSchedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ChatRoom extends Model
{
    protected $table = 'chat_rooms';

    protected $fillable = [
        'schedule_id',
        'slug',
        'title',
        'created_by_type', // FQCN: App\Models\Speaker | App\Models\Client | App\Models\Sponsor
        'created_by_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (ChatRoom $room) {
            if (empty($room->slug)) {
                $base = $room->title ? Str::slug($room->title) : 'room-'.$room->schedule_id;
                $slug = $base; $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $room->slug = $slug;
            }
        });
    }

    /** الروم تتبع سكجوال الحدث */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(EventSchedule::class, 'schedule_id');
    }

    /** المشاركون */
    public function participants(): HasMany
    {
        return $this->hasMany(ChatParticipant::class, 'room_id');
    }

    /** الرسائل */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'room_id');
    }
// App\Models\Chat\ChatRoom.php

public function latestMessage()
{
    return $this->hasOne(\App\Models\Chat\ChatMessage::class, 'room_id')->latestOfMany();
}

    /** سكوپ مختصر */
    public function scopeActive($q) { return $q->where('is_active', true); }
}
