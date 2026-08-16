<?php

namespace App\Http\Resources\Speaker;

use Illuminate\Http\Resources\Json\JsonResource;

class SpeakerResource extends JsonResource
{
    public function toArray($request)
    {
        // Resolve language from header (default en). Also accept ?lang=ar|en
        $lang = $request->header('Accept-Language', $request->query('lang', 'en'));
        $lang = in_array(strtolower($lang), ['ar','en'], true) ? strtolower($lang) : 'en';

        // Localized fields
        $name         = $lang === 'ar' ? ($this->name_ar ?? $this->name_en) : ($this->name_en ?? $this->name_ar);
        $title        = $lang === 'ar' ? ($this->title_ar ?? $this->title_en) : ($this->title_en ?? $this->title_ar);
        $company_name = $lang === 'ar' ? ($this->company_name_ar ?? $this->company_name_en) : ($this->company_name_en ?? $this->company_name_ar);

        return [
            'id'                    => $this->id,
            'lang'                  => $lang,

            // localized projections
            'name'                  => $name,
            'title'                 => $title,
            'company_name'          => $company_name,

            // raw (non-localized) metadata
            'image'                 => $this->image,
            'linkedin'              => $this->linkedin,
            'social_links'          => $this->social_links,
            'youtube'               => $this->youtube,
            'facebook'              => $this->facebook,
            'tiktok'                => $this->tiktok,
            'instgram'              => $this->instgram, // as per your field name
            'country_code'          => $this->country_code,
            'number_of_followers'   => $this->number_of_followers,
            'followers_ticktock'   => $this->followers_ticktock,
'section'=>$this->section??'',
            'orders'                => $this->orders,
            'type'                  => (int) $this->type,

            // timestamps
            'created_at'            => optional($this->created_at)->toIso8601String(),
            'updated_at'            => optional($this->updated_at)->toIso8601String(),

            // SECURITY: we do NOT expose password ever.
            // 'email' can be exposed only if you decide to allow it; default: hidden for public API
            // 'email'               => $this->when(auth()->guard('admin')->check(), $this->email),
        ];
    }
}
