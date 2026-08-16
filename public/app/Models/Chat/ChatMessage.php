<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ChatMessage extends Model
{
    protected $table = 'chat_messages';

    protected $fillable = [
        'room_id',
        'sender_type',   // FQCN: App\Models\Client | App\Models\Sponsor | App\Models\Speaker
        'sender_id',
        'message',
        'attachments',   // array JSON
        'replied_to_id',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class, 'room_id');
    }

    /** المُرسل polymorphic */
    public function sender(): MorphTo
    {
        // يعتمد على sender_type (FQCN) + sender_id
        return $this->morphTo(__FUNCTION__, 'sender_type', 'sender_id');
    }

    /** الرسالة التي نرد عليها */
    public function repliedTo(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'replied_to_id');
    }
}
