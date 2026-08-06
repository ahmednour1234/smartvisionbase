<?php

namespace App\Services;

use App\Models\LeadActivity;

class LeadActivityService
{
    public static function add(int $leadId, ?int $userId, string $type, ?string $message = null, array $meta = []): void
    {
        LeadActivity::create([
            'lead_id' => $leadId,
            'user_id' => $userId,
            'activity_type' => $type,
            'message' => $message,
            'meta' => (empty($meta) ? null : $meta),
        ]);
    }
}
