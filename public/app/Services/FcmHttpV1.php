<?php
// app/Services/FcmHttpV1.php
namespace App\Services;

class FcmHttpV1
{
    private string $projectId;
    private GoogleAccessToken $token;

    public function __construct(GoogleAccessToken $token)
    {
        $this->projectId = (string) env('FIREBASE_PROJECT_ID', '');
        if ($this->projectId === '') throw new \RuntimeException('FIREBASE_PROJECT_ID missing');
        $this->token = $token;
    }

    public function sendToTokens(array $tokens, array $payload): array
    {
        $tokens = array_values(array_filter(array_unique($tokens)));
        if (empty($tokens)) return ['success'=>0,'failure'=>0,'responses'=>[]];

        $endpoint = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
        $accessToken = $this->token->getToken();

        $responses = [];
        $success = $failure = 0;

        foreach ($tokens as $token) {
            $message = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => (string)($payload['title'] ?? ''),
                        'body'  => (string)($payload['body']  ?? ''),
                    ],
                    'data' => array_filter((array)($payload['data'] ?? []), fn($v)=>$v!==null && $v!==''),
                    // إعدادات منصة (اختياري):
                    // 'android' => ['priority'=>'HIGH'],
                    // 'apns'    => ['headers'=>['apns-priority'=>'10'], 'payload'=>['aps'=>['content-available'=>1]]],
                ],
            ];

            $res = $this->httpPostJson($endpoint, $message, [
                'Authorization: Bearer '.$accessToken,
                'Content-Type: application/json; charset=UTF-8',
            ]);

            $ok = isset($res['name']); // v1 بيرجع name عند النجاح
            $ok ? $success++ : $failure++;
            $responses[] = $res;
        }

        return compact('success','failure','responses');
    }

    private function httpPostJson(string $url, array $json, array $headers): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => json_encode($json, JSON_UNESCAPED_UNICODE),
            CURLOPT_TIMEOUT        => 20,
        ]);
        $raw = curl_exec($ch);
        $err = curl_errno($ch) ? curl_error($ch) : null;
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$raw) return ['http_error'=>$err, 'code'=>$code];
        $resp = json_decode($raw, true);
        if ($code >= 400) return ['code'=>$code, 'error'=>$resp ?? $raw];
        return $resp ?? ['raw'=>$raw, 'code'=>$code];
    }
}
