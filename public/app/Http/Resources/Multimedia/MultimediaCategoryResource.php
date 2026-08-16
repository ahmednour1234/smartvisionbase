<?php

namespace App\Http\Resources\Multimedia;

use Illuminate\Http\Resources\Json\JsonResource;

class MultimediaCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        $lang = $request->header('Accept-Language', $request->query('lang', 'en'));
        $lang = in_array(strtolower($lang), ['ar','en'], true) ? strtolower($lang) : 'en';

        $name        = $lang === 'ar' ? ($this->name_ar ?? $this->name_en) : ($this->name_en ?? $this->name_ar);
        $description = $lang === 'ar' ? ($this->description_ar ?? $this->description_en) : ($this->description_en ?? $this->description_ar);

        return [
            'id'          => $this->id,
            'lang'        => $lang,
            'name'        => $name,
            'description' => $description,
            'logo'        => $this->logo,
            'promo'       => $this->promo,
            'active'      => (int) $this->active,
            'created_at'  => optional($this->created_at)->toIso8601String(),
            'updated_at'  => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
