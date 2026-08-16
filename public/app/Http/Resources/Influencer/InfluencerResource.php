<?php

namespace App\Http\Resources\Influencer;

use Illuminate\Http\Resources\Json\JsonResource;

class InfluencerResource extends JsonResource
{
    public function toArray($request)
    {
        // استخراج اللغة من Accept-Language أو ?lang=
        $lang = $request->header('Accept-Language', $request->query('lang', 'en'));
        $lang = in_array(strtolower($lang), ['ar','en'], true) ? strtolower($lang) : 'en';

        // اختيار الحقول بحسب اللغة
        $name         = $this->{"name_{$lang}"} ?? $this->name_en ?? $this->name_ar;
        $title        = $this->{"title_{$lang}"} ?? $this->title_en ?? $this->title_ar;
        $description  = $this->{"description_{$lang}"} ?? $this->description_en ?? $this->description_ar;

        // بناء رابط الصورة (لو مش URL كامل)
        $image = $this->image;
        if ($image && ! str_starts_with($image, 'http')) {
            $image = url('public/'.$image);
        }

        return [
            'id'                   => (int) $this->id,
            'name'                 => $name,
            'title'                => $title,
            'description'          => $description,
            'country'              => $this->country,
            'count_vote'           => (int) ($this->count_vote ?? 0),
            'link'                 => $this->link,
            'image'                => $image,
            'active'               => (bool) $this->active,
            'regulation'           => $this->regulation,
            'stars'                => (int) ($this->stars ?? 0),
            'category'             => $this->category,
            'orders'               => (int) ($this->orders ?? 0),
            'number_of_followers'  => (int) ($this->number_of_followers ?? 0),

            // حقول اختيارية مفيدة
            'created_at'           => optional($this->created_at)->toISOString(),
            'updated_at'           => optional($this->updated_at)->toISOString(),
        ];
    }
}
