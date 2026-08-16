<?php
// app/Http/Resources/Booking/BookingResource.php

namespace App\Http\Resources\Booking;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Speaker\SpeakerResource;
use App\Http\Resources\Client\ClientResource;
use App\Http\Resources\SpeakerTime\SpeakerTimeResource;
use App\Http\Resources\Sponsor\SponsorResource;
class BookingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'speaker_id'      => (int) $this->speaker_id,
            'client_id'       => (int) $this->client_id,
            'speaker_time_id' => $this->speaker_time_id ? (int) $this->speaker_time_id : null,
            'time_from'       => $this->time_from,
            'time_to'         => $this->time_to,
            'status'          => (string) $this->status, // pending|confirmed|cancelled
            'active'          => (bool) $this->active,

            // “ميعاد الحجز فين” => داخل أي فتحة + تفاصيلها (اليوم/من/إلى)
            'slot'            => $this->whenLoaded('speakerTime', fn() => new SpeakerTimeResource($this->speakerTime)),

            // كيانات مرتبطة
            'speaker'         => $this->whenLoaded('speaker', fn() => new SpeakerResource($this->speaker)),
            'client'          => $this->whenLoaded('client',  fn() => new ClientResource($this->client)),
               'sponsor'   => $this->whenLoaded('sponsor', fn() => new SponsorResource($this->sponsor)),

            'created_at'      => optional($this->created_at)->toIso8601String(),
            'updated_at'      => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
