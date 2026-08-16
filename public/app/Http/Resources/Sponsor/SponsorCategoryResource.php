<?php

namespace App\Http\Resources\Sponsor;

use Illuminate\Http\Resources\Json\JsonResource;

class SponsorCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        // Accept-Language: ar|en (default en). Also allow ?lang=
        $lang = $request->header('Accept-Language', $request->query('lang', 'en'));
        $lang = in_array(strtolower($lang), ['ar','en'], true) ? strtolower($lang) : 'en';

        // في موديلك: name (عربي)، name_en (إنجليزي)
        $nameLocalized = $lang === 'ar'
            ? ($this->name ?? $this->name_en)
            : ($this->name_en ?? $this->name);

        return [
            'id'       => $this->id,
            'lang'     => $lang,
            'name'     => $nameLocalized,
            'logo'     => $this->logo,
            'active'   => (int) $this->active,
            'orders'   => (int) $this->orders,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
