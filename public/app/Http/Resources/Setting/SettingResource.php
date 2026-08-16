<?php

namespace App\Http\Resources\Setting;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    public function toArray($request)
    {
        $imgPath       = 'public/'.$this->img_mobile ?: null;
        $floorPlanPath =  'public/'.$this->img_mobile ?: null;

        $imgUrl       = $imgPath       ? (str_starts_with($imgPath, 'http') ? $imgPath : url($imgPath)) : null;
        $floorPlanUrl = $floorPlanPath ? (str_starts_with($floorPlanPath, 'http') ? $floorPlanPath : url($floorPlanPath)) : null;

        return [
            'id'          => (int) $this->id,
            'name'        => (string) $this->name,
            'phone'       => (string) $this->phone,
            'address'     => $this->address,
            'email'       => $this->email,
            'lat'         => $this->lat,
            'lng'         => $this->lang ?? $this->lng ?? null, // دعم الحقل المسمى lang بالخطأ
            'voting'      => filter_var($this->voting, FILTER_VALIDATE_BOOL),

            // صور
            'image'       => $imgPath,
            'image_url'   => $imgUrl,
            'floor_plan'  => $floorPlanPath,
            'floor_plan_url' => $floorPlanUrl,

            // السوشيال
            'social'      => [
                'facebook'  => $this->facebook,
                'instagram' => $this->instagram,
                'linkedin'  => $this->linkedin,
                'x'         => $this->x,
                'youtube'   => $this->youtube,
                'flickr'    => $this->flickr,
            ],
            'about'=>$this->about ??'',
            'privacy'=>$this->privacy??'',
            'terms'=>$this->terms??'',
'website'=>'https://affiliatesummitglobal.com/',
            'created_at'  => optional($this->created_at)->toISOString(),
            'updated_at'  => optional($this->updated_at)->toISOString(),
        ];
    }
}
