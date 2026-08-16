<?php

namespace App\Http\Resources\Event;

use Illuminate\Http\Resources\Json\JsonResource;

class EventScheduleSpeakerResource extends JsonResource
{
    public function toArray($request)
    {
        $lang = $request->header('Accept-Language', $request->query('lang', 'en'));
        $lang = in_array(strtolower($lang), ['ar','en'], true) ? strtolower($lang) : 'en';

        $name = $lang === 'ar'
            ? ($this->name_ar ?? $this->name_en)
            : ($this->name_en ?? $this->name_ar);

        return [
            'id'    => $this->id,
            'name'  => $name,
            'image' => $this->image,
        ];
    }
}
