<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('chat.room.{roomId}', function ($user, $roomId) {
    // تحقّق إن المستخدم عضو في الغرفة
    return \App\Models\Chat\ChatParticipant::query()
        ->where('room_id', (int)$roomId)
        ->when($user, function ($q) use ($user) {
            // نحاول نفهم نوع الحارس/الموديل
            $base = strtolower(class_basename($user));
            $map  = ['client' => 'client', 'sponsor' => 'sponsor', 'speaker' => 'speaker'];
            $short = $map[$base] ?? ($user->type ?? 'client');

            return $q->where('participant_type', $short)
                     ->where('participant_id', (int)($user->getAuthIdentifier()));
        })
        ->exists();
});
