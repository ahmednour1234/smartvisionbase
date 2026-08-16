<?php

namespace App\Http\Resources\Multimedia;

use Illuminate\Http\Resources\Json\JsonResource;

class MultiMediaResource extends JsonResource
{
    public function toArray($request)
    {
        $lang = $request->header('Accept-Language', $request->query('lang', 'en'));
        $lang = in_array(strtolower($lang), ['ar','en'], true) ? strtolower($lang) : 'en';

        $name = $lang === 'ar' ? ($this->name_ar ?? $this->name_en) : ($this->name_en ?? $this->name_ar);

        return [
            'id'        => $this->id,
            'lang'      => $lang,

            'name'      => $name,
            'images'    => $this->images ?? [],
            'links'     => $this->links ?? [],
            'date'      => optional($this->date)->toDateString(),
            'active'    => (int) $this->active,

            'category_id' => $this->multi_media_category_id,
            'category'    => new MultimediaCategoryResource($this->whenLoaded('category')),

            'created_at'  => optional($this->created_at)->toIso8601String(),
            'updated_at'  => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
