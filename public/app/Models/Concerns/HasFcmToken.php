<?php
// app/Models/Concerns/HasFcmToken.php
namespace App\Models\Concerns;

trait HasFcmToken
{
    public function getFcmTokens(): array
    {
        $t = trim((string) ($this->fcm_token ?? ''));
        return $t !== '' ? [$t] : [];
    }
}
