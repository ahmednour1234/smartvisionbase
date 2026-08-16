<?php
// app/Http/Resources/SpeakerTime/SpeakerTimeResource.php

namespace App\Http\Resources\SpeakerTime;

use Illuminate\Http\Resources\Json\JsonResource;

class SpeakerTimeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'speaker_id' => (int) $this->speaker_id,
            'date'       => optional($this->date)->format('Y-m-d') ?? (string)$this->date,
            'time_from'  => (string) $this->time_from,
            'time_to'    => (string) $this->time_to,
            'status'     => (string) $this->status, // available|booked|unavailable
            'active'     => (bool) $this->active,

            // مشتقات مفيدة
            'is_available' => ($this->status === 'available') && (bool)$this->active,
            'created_at'   => optional($this->created_at)->toIso8601String(),
            'updated_at'   => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
