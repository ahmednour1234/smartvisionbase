<?php

namespace App\Http\Resources\Package;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    public function toArray($request)
    {
        // Language from header or ?lang= (default en)
        $lang = $request->header('Accept-Language', $request->query('lang', 'en'));
        $lang = in_array(strtolower($lang), ['ar','en'], true) ? strtolower($lang) : 'en';

        $name        = $lang === 'ar' ? ($this->name_ar ?? $this->name_en) : ($this->name_en ?? $this->name_ar);
        $title       = $lang === 'ar' ? ($this->title_ar ?? $this->title_en) : ($this->title_en ?? $this->title_ar);
        $description = $lang === 'ar' ? ($this->description_ar ?? $this->description_en) : ($this->description_en ?? $this->description_ar);

        return [
            'id'               => $this->id,
            'lang'             => $lang,

            'name'             => $name,
            'title'            => $title,
'description_html' => nl2br(e($description)),

            'price'            => (float) $this->price,
            'discount_price'   => $this->discount_price !== null ? (float) $this->discount_price : null,
            'duration'         => $this->duration,
            'image'            => $this->image,

            'sort'             => (int) $this->sort,
            'active'           => (int) $this->active,

            'created_at'       => optional($this->created_at)->toIso8601String(),
            'updated_at'       => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
