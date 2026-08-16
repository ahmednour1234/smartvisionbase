<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WaWebhookController extends Controller
{
    // GET verify
    public function verify(Request $req)
    {
        $mode      = $req->query('hub_mode') ?? $req->query('hub.mode');
        $token     = $req->query('hub_verify_token') ?? $req->query('hub.verify_token');
        $challenge = $req->query('hub_challenge') ?? $req->query('hub.challenge');

        if ($mode === 'subscribe' && $token === env('WA_WEBHOOK_VERIFY_TOKEN')) {
            return response((string)$challenge, 200)->header('Content-Type', 'text/plain');
        }
        return response('Forbidden', 403);
    }

    // POST receive
    public function receive(Request $req)
    {
        // (اختياري) توقيع الأمان
        $appSecret = env('META_APP_SECRET');
        if ($appSecret) {
            $sig  = $req->header('X-Hub-Signature-256');
            $calc = 'sha256=' . hash_hmac('sha256', $req->getContent(), $appSecret);
            if (!hash_equals($calc, (string)$sig)) {
                Log::warning('WA webhook: invalid signature');
                return response('Invalid signature', 401);
            }
        }

        $payload = $req->json()->all();
        Log::info('WA webhook event', $payload);

        // === حالات الإرسال (outbound) ===
        $statuses = data_get($payload, 'entry.0.changes.0.value.statuses', []);
        foreach ($statuses as $st) {
            $wamid  = data_get($st, 'id');
            $stat   = data_get($st, 'status'); // sent|delivered|read|failed|deleted
            $reason = data_get($st, 'error_data.details') ?? data_get($st, 'errors.0.details');

            Log::info('WA status', compact('wamid','stat','reason'));

            // نجاح ⇒ امسح سياق fallback
            if (in_array($stat, ['delivered', 'read'], true)) {
                Cache::forget("wa:outbox:{$wamid}");
                continue;
            }

            // فشل ⇒ جرّب Template (مع إزالة الهيدر تلقائيًا لو 132018)
            if ($stat === 'failed') {
                $ctx = Cache::pull("wa:outbox:{$wamid}");
                if ($ctx) {
                    Log::warning('WA session failed, doing template fallback', ['wamid' => $wamid, 'reason' => $reason]);

                    $phoneId = env('META_PHONE_NUMBER_ID', '');
                    $token   = env('META_WA_TOKEN', '');
                    $tplName = $ctx['template'] ?? env('WA_TEMPLATE_NAME', 'event_new');
                    $tplLang = $ctx['lang']     ?? env('WA_TEMPLATE_LANG', 'en');

                    $components = [];

                    // جرّب بالهيدر أولًا لو عندنا ميديا
                    if (!empty($ctx['media_id']) || !empty($ctx['media_link'])) {
                        $headerParam = !empty($ctx['media_id'])
                            ? ['type' => 'image', 'image' => ['id' => $ctx['media_id']]]
                            : ['type' => 'image', 'image' => ['link' => $ctx['media_link']]];
                        $components[] = ['type' => 'header', 'parameters' => [ $headerParam ]];
                    }

                    $components[] = [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => ($ctx['name'] ?? 'Guest')],
                            ['type' => 'text', 'text' => ($ctx['message_text'] ?? '')],
                        ],
                    ];

                    $payloadTpl = [
                        'messaging_product' => 'whatsapp',
                        'to'       => $ctx['to'],
                        'type'     => 'template',
                        'template' => [
                            'name'     => $tplName,
                            'language' => ['code' => $tplLang],
                            'components' => $components,
                        ],
                    ];

                    $res = Http::withToken($token)->post("https://graph.facebook.com/v23.0/{$phoneId}/messages", $payloadTpl);
                    $status = $res->status();
                    $json   = $res->json();
                    Log::info('WA template fallback response', ['status' => $status, 'resp' => $json]);

                    // لو خطأ 132018 للهيدر ⇒ ابعت تاني من غير هيدر
                    $code    = (int) data_get($json, 'error.code', 0);
                    $details = strtolower((string) data_get($json, 'error.error_data.details', ''));
                    if ($status === 400 && $code === 132018 && str_contains($details, 'header')) {
                        Log::warning('Template header not allowed on fallback – retry without header.');
                        $payloadTpl['template']['components'] = [
                            [
                                'type' => 'body',
                                'parameters' => [
                                    ['type' => 'text', 'text' => ($ctx['name'] ?? 'Guest')],
                                    ['type' => 'text', 'text' => ($ctx['message_text'] ?? '')],
                                ],
                            ]
                        ];
                        $res2 = Http::withToken($token)->post("https://graph.facebook.com/v23.0/{$phoneId}/messages", $payloadTpl);
                        Log::info('WA template fallback (no header) response', ['status' => $res2->status(), 'resp' => $res2->json()]);
                    }
                } else {
                    Log::info('No outbox context found for wamid', ['wamid' => $wamid]);
                }
            }
        }

        // === رسائل واردة (inbound) ===
        $messages = data_get($payload, 'entry.0.changes.0.value.messages', []);
        foreach ($messages as $m) {
            $from = preg_replace('/\D+/', '', (string) data_get($m, 'from'));
            Log::info('WA inbound', [
                'from'  => $from,
                'type'  => data_get($m, 'type'),
                'text'  => data_get($m, 'text.body'),
                'wamid' => data_get($m, 'id'),
            ]);

            // افتح نافذة 24 ساعة لهذا الرقم
            if ($from) {
                Cache::put("wa:window:{$from}", true, now()->addHours(24));
            }
        }

        return response('EVENT_RECEIVED', 200);
    }
}
