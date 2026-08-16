<?php

namespace App\Http\Resources\Qrcode;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QrcodeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'image_url'   => 'qrcodes/'.$this->qrcode,
            'active'      => (bool) $this->active,
            'scan'        => (int) ($this->scan ?? 0),
            'register_id' => (int) $this->register_id,
            'email'       => $this->email,
            'short_code'  => $this->short_code,
            'scan_at'     => optional($this->scan_at)->toIso8601String(),
            'created_at'  => optional($this->created_at)->toIso8601String(),
            'updated_at'  => optional($this->updated_at)->toIso8601String(),
        ];
    }

    protected function imageUrl(?string $value): ?string
    {
        if (!$value) return null;

        // لو القيمة URL كامل — ارجعه كما هو
        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        // لو القيمة Path داخل تخزين public
        if (Storage::disk('public')->exists($value)) {
            return Storage::disk('public')->url($value);
        }

        // fallback: جرّب storage الافتراضي
        if (Storage::exists($value)) {
            return Storage::url($value);
        }

        // وإلا رجّع القيمة كما هي
        return $value;
    }
}
