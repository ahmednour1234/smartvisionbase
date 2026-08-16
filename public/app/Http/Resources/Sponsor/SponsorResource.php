<?php

namespace App\Http\Resources\Sponsor;

use Illuminate\Http\Resources\Json\JsonResource;

class SponsorResource extends JsonResource
{
    public function toArray($request)
    {
        $lang = $request->header('Accept-Language', $request->query('lang', 'en'));
        $lang = in_array(strtolower($lang), ['ar','en'], true) ? strtolower($lang) : 'en';

        // Localized fields from sponsor
        $name         = $lang === 'ar' ? ($this->name_ar ?? $this->name_en) : ($this->name_en ?? $this->name_ar);
        $title        = $lang === 'ar' ? ($this->title_ar ?? $this->title_en) : ($this->title_en ?? $this->title_ar);
        $company_name = $lang === 'ar' ? ($this->company_name_ar ?? $this->company_name_en) : ($this->company_name_en ?? $this->company_name_ar);

        return [
            'id'              => $this->id,
            'lang'            => $lang,

            'name'            => $name,
            'title'           => $title,
            'company_name'    => $company_name,

            // other fields
            'image'           => $this->image,
            'phone'           => $this->phone,
            'link_profile'    => $this->link_profile,
            'active'          => (int) $this->active,
            'orders'          => (int) $this->orders,
            'category_id'     => $this->category_sponsor_id,

            // nested category (localized inside its resource)
            'category'        => new SponsorCategoryResource($this->whenLoaded('category')),

            'created_at'      => optional($this->created_at)->toIso8601String(),
            'updated_at'      => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
