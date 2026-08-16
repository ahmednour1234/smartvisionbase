<?php
// app/Http/Controllers/MetaCapiRawController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MetaCapiController extends Controller
{
    public function send(Request $request)
    {
        $pixelId     = (string) config('services.meta.pixel_id');
        $accessToken = (string) config('services.meta.capi_token');
        $apiVersion  = (string) config('services.meta.api_version', 'v23.0');

        if (!$pixelId || !$accessToken) {
            return response()->json([
                'ok' => false,
                'error' => 'Missing META pixel_id or capi_token in config/services.php',
            ], 422);
        }

        // 1) اجمع المدخلات
        $eventName = (string) $request->input('event_name', 'Lead');
        $eventId   = $request->input('event_id');           // اختياري (لدوبلكيشن)
        $value     = (float) $request->input('value', 0);
        $currency  = (string) $request->input('currency', 'USD');

        // fbc/fbp: لو مش مبعوتين في البودي، جرّب من الكوكي
        $fbc = $request->input('fbc') ?? $request->cookie('_fbc');
        $fbp = $request->input('fbp') ?? $request->cookie('_fbp');

        // بيانات المطابقة (لو واصلة خام هنشفّرها SHA-256 lowercase + trim)
        $hashIfNeeded = function (?string $val) {
            if (!$val) return null;
            $v = trim(Str::lower($val));
            return preg_match('/^[a-f0-9]{64}$/', $v) ? $v : hash('sha256', $v);
        };

        $email = $hashIfNeeded($request->input('em') ?? $request->input('email'));
        $phone = $hashIfNeeded($request->input('ph') ?? $request->input('phone'));

        // client info
        $clientIp  = $request->ip();
        $userAgent = $request->header('User-Agent');

        // 2) ابنِ الـ payload
        $eventTime = time(); // مهم ألا يكون مستقبل
        $userData  = array_filter([
            'em'                  => $email ? [$email] : null,
            'ph'                  => $phone ? [$phone] : null,
            'fbc'                 => $fbc,
            'fbp'                 => $fbp,
            'client_ip_address'   => $clientIp,
            'client_user_agent'   => $userAgent,
        ]);

        $customData = array_filter([
            'currency'     => $currency,
            'value'        => $value,
            'content_name' => $request->input('content_name'),
        ]);

        $event = [
            'event_name'    => $eventName,
            'event_time'    => $eventTime,
            'action_source' => 'website',
            'user_data'     => $userData,
            'custom_data'   => $customData,
        ];

        if (!empty($eventId)) {
            $event['event_id'] = $eventId;
        }

        $payload = ['data' => [ $event ]];

        // test_event_code للمعاينة داخل Test Events
        if ($tec = $request->input('test_event_code')) {
            $payload['test_event_code'] = $tec;
        }

        // 3) نفّذ cURL POST (بدون أي لايبراري)
        $endpoint = "https://graph.facebook.com/{$apiVersion}/{$pixelId}/events?access_token=" . urlencode($accessToken);

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 20,
        ]);

        $raw  = curl_exec($ch);
        $errno = curl_errno($ch);
        $err   = curl_error($ch);
        $http  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno) {
            // خطأ شبكة/اتصال
            return response()->json([
                'ok'      => false,
                'status'  => 0,
                'error'   => "cURL error #{$errno}: {$err}",
                'sent'    => $payload,
                'endpoint'=> $endpoint,
            ], 500);
        }

        $json = json_decode($raw, true);

        if ($http < 200 || $http >= 300) {
            // أخطاء Graph API (زي Unsupported post request / permissions)
            return response()->json([
                'ok'       => false,
                'status'   => $http,
                'endpoint' => $endpoint,
                'hint'     => 'Verify Pixel ID, token ownership & permissions (ads_management), and Business match.',
                'meta'     => $json,
                'sent'     => $payload,
            ], $http);
        }

        return response()->json([
            'ok'   => true,
            'meta' => $json,
        ], 200);
    }
}
