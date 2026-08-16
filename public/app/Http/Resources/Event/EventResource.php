<?php

namespace App\Http\Resources\Event;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class EventResource extends JsonResource
{
    public function toArray($request)
    {
        // اللغة من الهيدر أو ?lang= — الافتراضي en
        $lang = $request->header('Accept-Language', $request->query('lang', 'en'));
        $lang = in_array(strtolower($lang), ['ar','en'], true) ? strtolower($lang) : 'en';

        // حقول محلية اللغة (مع fallback ذكي)
        $name        = $lang === 'ar' ? ($this->name_ar ?? $this->name_en) : ($this->name_en ?? $this->name_ar);
        $description = $lang === 'ar' ? ($this->description_ar ?? $this->description_en) : ($this->description_en ?? $this->description_ar);
        $address     = $lang === 'ar' ? ($this->address_ar ?? $this->address_en) : ($this->address_en ?? $this->address_ar);

        // تنسيق التواريخ
        $eventDateIso = $this->event_date ? Carbon::parse($this->event_date)->toIso8601String() : null;
        $endDateIso   = $this->end_date   ? Carbon::parse($this->end_date)->toIso8601String()   : null;

        return [
            'id'               => $this->id,
            'lang'             => $lang,

            'name'             => $name,
            'description'      => $description,
            'address'          => $address,

            'event_date'       => $eventDateIso,              // ISO-8601
            'end_date'         => $endDateIso,                // ISO-8601

            'location'         => $this->location,            // اتركها كما هي (string/JSON حسب التخزين)
            'attendees_limit'  => $this->attendees_limit !== null ? (int) $this->attendees_limit : null,
            'main_image'       => $this->main_image,
            'active'           => (int) $this->active,
            'text_email'       => $this->text_email,

            'created_at'       => optional($this->created_at)->toIso8601String(),
            'updated_at'       => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
