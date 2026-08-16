<?php

namespace App\Http\Resources\Chat;

use App\Support\UserNameResolver;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatParticipantResource extends JsonResource
{
    public function toArray($request)
    {
        $lang = $request->header('Accept-Language', $request->query('lang', 'en'));
        $lang = in_array(strtolower($lang), ['ar','en'], true) ? strtolower($lang) : 'en';

        return [
            'id'                 => (int) $this->id,
            'room_id'            => (int) $this->room_id,
            'participant_type'   => $this->participant_type,
            'participant_id'     => (int) $this->participant_id,
            'participant_name'   => UserNameResolver::name($this->participant_type, (int) $this->participant_id, $lang),
            'joined_at'          => optional($this->joined_at)->toISOString(),
            'last_read_message_id' => $this->last_read_message_id ? (int) $this->last_read_message_id : null,
            'created_at'         => optional($this->created_at)->toISOString(),
        ];
    }
}
