<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Setting\SettingResource;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class PublicSettingController extends Controller
{
    /**
     * GET /api/settings
     * يرجّع أول سجل إعدادات كـ JSON (بدون Auth).
     */
    public function show(): JsonResponse|\App\Http\Resources\Setting\SettingResource
    {
        $settings = Setting::query()->first();

        if (!$settings) {
            return response()->json([
                'data'    => null,
                'message' => 'No settings found.',
            ], 404);
        }

        return new SettingResource($settings);
    }
}
