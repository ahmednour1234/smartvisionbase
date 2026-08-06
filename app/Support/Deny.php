<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class Deny
{
    public static function hiddenOrForbidden(?string $forbiddenMessage = null): JsonResponse
    {
        if (config('crm.security.hide_existence')) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(['message' => $forbiddenMessage ?? 'Forbidden'], 403);
    }

    public static function unauthorized(): JsonResponse
    {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
