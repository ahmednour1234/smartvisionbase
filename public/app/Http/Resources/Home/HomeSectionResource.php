<?php

namespace App\Http\Resources\Home;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeSectionResource extends JsonResource
{
    public function toArray($request)
    {
        // اللغة من الهيدر أو ?lang= — الافتراضي en
        $lang = $request->header('Accept-Language', $request->query('lang', 'en'));
        $lang = in_array(strtolower($lang), ['ar','en'], true) ? strtolower($lang) : 'en';

        // الحقول المخزّنة كـ JSON: title/description
        $title       = $this->pickLocale($this->title, $lang);
        $description = $this->pickLocale($this->description, $lang);

        return [
            'id'            => $this->id,
            'lang'          => $lang,

            'title'         => $title,
            'description'   => $description,

            'media_type'    => $this->media_type,   // image | video | html | ...
            'media_path'    => $this->media_path,   // URL / path
            'thumbnail'     => $this->thumbnail,

            'section_order' => (int) $this->section_order,
            'is_active'     => (int) $this->is_active,

            'created_at'    => optional($this->created_at)->toIso8601String(),
            'updated_at'    => optional($this->updated_at)->toIso8601String(),
        ];
    }

    /**
     * يختار القيمة المناسبة من JSON بحسب اللغة مع fallback ذكي
     * مثال: ["en" => "...", "ar" => "..."]
     */
    protected function pickLocale($json, string $lang): ?string
    {
        if (!is_array($json) || empty($json)) {
            return is_string($json) ? $json : null;
        }

        // لو موجود المفتاح المطلوب
        if (isset($json[$lang]) && $json[$lang] !== '') {
            return $json[$lang];
        }

        // جرّب الإنجليزية
        if (isset($json['en']) && $json['en'] !== '') {
            return $json['en'];
        }

        // رجّع أول قيمة غير فاضية
        foreach ($json as $v) {
            if (is_string($v) && trim($v) !== '') {
                return $v;
            }
        }

        return null;
    }
}
