<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WatiService
{
    protected string $baseUrl;
    protected string $token;
    protected ?string $channelNumber;

    public function __construct()
    {
        $this->baseUrl       = rtrim(config('services.wati.base_url', ''), '/');
        $this->token         = (string) config('services.wati.token', '');
        $this->channelNumber = config('services.wati.channel_number');
    }

    /** هل إعدادات WATI صالحة؟ */
    public function isReady(): bool
    {
        return $this->baseUrl !== '' && $this->token !== '';
    }

    /**
     * إرسال Template Message لشخص واحد.
     *
     * @param  string $toWhatsapp E.164 رقم المستلم (بدون مسافات)
     * @param  string $templateName
     * @param  string $broadcastName
     * @param  array  $parameters   [["name"=>"1","value"=>"..."], ...]
     * @param  string|null $headerImageUrl  لو التمبلت فيها Header Image (اختياري)
     *
     * @return array{ok:bool,status:int|null,body:array|null,error:string|null}
     */
    public function sendTemplate(
        string $toWhatsapp,
        string $templateName,
        string $broadcastName,
        array  $parameters,
        ?string $headerImageUrl = null
    ): array {
        if (!$this->isReady()) {
            $msg = 'WATI not configured (BASE_URL or TOKEN missing)';
            Log::warning($msg, ['to' => $toWhatsapp]);
            return ['ok' => false, 'status' => null, 'body' => null, 'error' => $msg];
        }

        if ($toWhatsapp === '') {
            $msg = 'WATI skipped: empty recipient phone';
            Log::warning($msg);
            return ['ok' => false, 'status' => null, 'body' => null, 'error' => $msg];
        }

        $url = $this->baseUrl . '/api/v1/sendTemplateMessage';
        $query = ['whatsappNumber' => $toWhatsapp]; // المستلم
        $payload = [
            'template_name'  => $templateName,
            'broadcast_name' => $broadcastName,
            'parameters'     => $parameters,
        ];

        // لو عندك أكتر من رقم/قناة على WATI
        if (!empty($this->channelNumber)) {
            $payload['channel_number'] = $this->channelNumber;
        }

        // لو التمبلت فيها Header Image، بعض حسابات WATI بتقبل header_values كمصفوفة URL واحدة
        if (!empty($headerImageUrl)) {
            $payload['header_values'] = [$headerImageUrl]; // مدعوم في مزودين كُثر، وWATI غالباً تتقبّله.
        }

        try {
            Log::info('WATI sendTemplate: request', ['url' => $url, 'query' => $query, 'payload' => $payload]);
            $res = Http::withToken($this->token)
                ->acceptJson()
                ->asJson()
                ->post($url . '?' . http_build_query($query), $payload);

            $status = $res->status();
            $json   = null;
            try { $json = $res->json(); } catch (\Throwable $e) {}

            if ($res->successful()) {
                Log::info('WATI sendTemplate: success', ['status' => $status, 'resp' => $json]);
                return ['ok' => true, 'status' => $status, 'body' => $json, 'error' => null];
            }

            Log::error('WATI sendTemplate: failed', [
                'status' => $status, 'resp' => $json, 'text' => $res->body()
            ]);
            return ['ok' => false, 'status' => $status, 'body' => $json, 'error' => 'WATI API error'];
        } catch (\Throwable $e) {
            Log::error('WATI sendTemplate: exception', ['error' => $e->getMessage()]);
            return ['ok' => false, 'status' => null, 'body' => null, 'error' => $e->getMessage()];
        }
    }

    /** أداة بسيطة لتنظيف رقم الموبايل (اختياري). */
    public static function sanitizeMsisdn(?string $raw): string
    {
        $n = preg_replace('/\D+/', '', (string)$raw);
        return $n ?? '';
    }
}
