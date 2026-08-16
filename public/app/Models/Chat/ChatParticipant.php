<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ChatParticipant extends Model
{
    protected $table = 'chat_participants';

    protected $fillable = [
        'room_id',
        'participant_type', // FQCN: App\Models\Client | App\Models\Sponsor | App\Models\Speaker
        'participant_id',
        'joined_at',
        'last_read_message_id',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class, 'room_id');
    }

    /** المشارك polymorphic */
    public function participant(): MorphTo
    {
        // يعتمد على participant_type (FQCN) + participant_id
        return $this->morphTo(__FUNCTION__, 'participant_type', 'participant_id');
    }

    /** آخر رسالة قراها المشارك (اختياري للـ eager load) */
    public function lastReadMessage(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'last_read_message_id');
    }
}
