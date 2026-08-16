<?php

namespace App\Events;

use App\Http\Resources\Chat\ChatMessageResource;
use App\Models\Chat\ChatMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(public ChatMessage $message) {}

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('chat.room.' . $this->message->room_id);
    }

    public function broadcastWith(): array
    {
        // رجّع الرسالة بصيغة الAPI اللي عميلك يعرفها
        return [
            'message' => (new ChatMessageResource($this->message->load('sender')))->resolve(),
        ];
    }
}
