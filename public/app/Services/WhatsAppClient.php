<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppClient
{
    protected string $base;
    protected string $token;
    protected string $phoneId;

    public function __construct()
    {
        $version       = config('services.whatsapp.version');
        $this->phoneId = (string) config('services.whatsapp.phone_id');
        $this->token   = (string) config('services.whatsapp.token');
        $this->base    = "https://graph.facebook.com/{$version}";
    }

    /**
     * إرسال Template (مثلاً hello_world)
     * $components اختياري لتمرير متغيرات البودي/الهيدر.
     */
    public function sendTemplate(string $to, string $templateName, string $langCode = 'en_US', array $components = []): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'to'       => $to,        // رقم دولي بدون رموز (E.164 digits)
            'type'     => 'template',
            'template' => array_filter([
                'name'       => $templateName,
                'language'   => ['code' => $langCode],
                'components' => $components ?: null,
            ]),
        ];

        $resp = Http::withToken($this->token)
            ->acceptJson()
            ->asJson()
            ->post("{$this->base}/{$this->phoneId}/messages", $payload);

        if (!$resp->successful()) {
            throw new \RuntimeException('WA sendTemplate error: '.$resp->body());
        }

        return $resp->json();
    }

    /**
     * تنسيق إلى E.164 (أرقام فقط): country_code + phone
     * يقصّي أي رموز مثل + أو مسافات أو 00.
     */
    public static function e164Digits(string $countryCode, string $phone): string
    {
        $cc = preg_replace('/\D+/', '', $countryCode);
        $ph = preg_replace('/\D+/', '', $phone);
        // لو المستخدم كتب الرقم كامل مسبقًا، نمنع التكرار
        if (str_starts_with($ph, $cc)) {
            return $ph;
        }
        return $cc.$ph;
    }
    public function requestCode(string $method = 'SMS', string $language = 'en'): array
{
    $resp = Http::withToken($this->token)
        ->post("{$this->base}/{$this->phoneId}/request_code", [
            'code_method' => strtoupper($method), // SMS | VOICE
            'language'    => $language,
        ]);
    if (!$resp->successful()) throw new \RuntimeException($resp->body());
    return $resp->json();
}

public function verifyCode(string $code): array
{
    $resp = Http::withToken($this->token)
        ->post("{$this->base}/{$this->phoneId}/verify_code", [
            'code' => $code, // 6 digits
        ]);
    if (!$resp->successful()) throw new \RuntimeException($resp->body());
    return $resp->json();
}

public function registerNumber(string $pin/* 6 digits */, bool $includeCert = false): array
{
    $body = [
        'messaging_product' => 'whatsapp',
        'pin'               => $pin,
    ];
    if ($includeCert) $body['certificate'] = 'cert'; // فقط لو واجهت Error يطلبها
    $resp = Http::withToken($this->token)
        ->post("{$this->base}/{$this->phoneId}/register", $body);

    if (!$resp->successful()) throw new \RuntimeException('Register failed: '.$resp->body());
    return $resp->json();
}

}
