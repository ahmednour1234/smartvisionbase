<?php

namespace App\Http\Resources\Blog;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class BlogResource extends JsonResource
{
    public function toArray($request)
    {
        // اللغة من الهيدر أو ?lang= — الافتراضي en
        $lang = $request->header('Accept-Language', $request->query('lang', 'en'));
        $lang = in_array(strtolower($lang), ['ar','en'], true) ? strtolower($lang) : 'en';

        $name        = $lang === 'ar' ? ($this->name_ar ?? $this->name_en) : ($this->name_en ?? $this->name_ar);
        $title       = $lang === 'ar' ? ($this->title_ar ?? $this->title_en) : ($this->title_en ?? $this->title_ar);
        $description = $lang === 'ar' ? ($this->description_ar ?? $this->description_en) : ($this->description_en ?? $this->description_ar);

        // تاريخ موحّد (لو العمود string/DATE سنحوّله YYYY-MM-DD)
        $date = $this->date ? Carbon::parse($this->date)->toDateString() : null;

        return [
            'id'           => $this->id,
            'lang'         => $lang,

            'name'         => $name,
            'title'        => $title,
            'description'  => $description,

            'image'        => $this->image,
            'link'         => $this->link,

            'date'         => $date,
            'sort'         => (int) $this->sort,
            'active'       => (int) $this->active, // سيكون دائمًا 1 في index/show

            'created_at'   => optional($this->created_at)->toIso8601String(),
            'updated_at'   => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
