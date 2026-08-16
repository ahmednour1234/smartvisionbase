<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\SponsorScheduleService;
use Illuminate\Http\Request;

class SponsorScheduleController extends Controller
{
    public function __construct(private SponsorScheduleService $service) {}

    /**
     * GET /api/sponsor/schedule?date=2025-11-05&status=pending&time_from=10:30&time_to=13:00
     * يتطلب مصادقة سبونسر (auth:sponsor أو حسب الجارد عندك)
     */
    public function index(Request $request)
    {
        $sponsor = $request->user('sponsor'); // عدّل للجارد المناسب
        if (!$sponsor) return response()->json(['message' => 'Unauthenticated'], 401);

        $filters = $request->only(['date','status','time_from','time_to']);
        $data = $this->service->sponsorSlots($sponsor->id, $filters);

        return response()->json(['data' => $data]);
    }
}
