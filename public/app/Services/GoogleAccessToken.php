<?php
// app/Services/GoogleAccessToken.php
namespace App\Services;

class GoogleAccessToken
{
    private string $saPath;
    private array $sa;

    public function __construct()
    {
        $this->saPath = base_path((string) env('FIREBASE_SA'));
        $json = file_get_contents($this->saPath);
        if (!$json) throw new \RuntimeException('Service Account JSON not found');
        $this->sa = json_decode($json, true) ?: [];
    }

    public function getToken(): string
    {
        // جرّب كاش بسيط في ملفات الـ tmp لمدة ~50 دقيقة
        $cacheFile = sys_get_temp_dir().'/gcp_token_'.md5($this->saPath).'.cache';
        if (is_file($cacheFile)) {
            $cached = json_decode(file_get_contents($cacheFile), true);
            if ($cached && ($cached['exp'] ?? 0) > time()+60) {
                return $cached['access_token'];
            }
        }

        $scope = 'https://www.googleapis.com/auth/firebase.messaging';
        $jwt = $this->makeJwt($scope);
        $tokenResp = $this->httpPost('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        $access = $tokenResp['access_token'] ?? null;
        $expiresIn = (int)($tokenResp['expires_in'] ?? 0);

        if (!$access) throw new \RuntimeException('Failed to obtain access_token');

        file_put_contents($cacheFile, json_encode([
            'access_token' => $access,
            'exp'          => time() + max(0, $expiresIn-30),
        ]));

        return $access;
    }

    private function makeJwt(string $scope): string
    {
        $now = time();
        $payload = [
            'iss'   => $this->sa['client_email'],
            'scope' => $scope,
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ];

        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $segments = [
            $this->b64(json_encode($header)),
            $this->b64(json_encode($payload)),
        ];
        $signingInput = implode('.', $segments);

        $privateKey = $this->sa['private_key'];
        $pkey = openssl_pkey_get_private($privateKey);
        if (!$pkey) throw new \RuntimeException('Invalid private key');

        $signature = '';
        openssl_sign($signingInput, $signature, $pkey, 'sha256');
        $segments[] = $this->b64($signature);

        return implode('.', $segments);
    }

    private function b64(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function httpPost(string $url, array $form): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_POSTFIELDS     => http_build_query($form),
            CURLOPT_TIMEOUT        => 15,
        ]);
        $raw = curl_exec($ch);
        $err = curl_errno($ch) ? curl_error($ch) : null;
        curl_close($ch);
        if (!$raw) throw new \RuntimeException('Token HTTP error: '.$err);
        return json_decode($raw, true) ?: [];
    }
}
