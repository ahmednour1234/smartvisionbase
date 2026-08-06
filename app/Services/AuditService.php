<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditService
{
    public static function log(?int $userId, string $action, string $entity, ?int $entityId = null, array $meta = [], ?string $ip = null): void
    {
        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'meta' => (empty($meta) ? null : $meta),
            'ip' => $ip,
        ]);
    }

    public static function ip(Request $request): string
    {
        return (string)($request->ip() ?? $request->server('REMOTE_ADDR') ?? '');
    }
}
