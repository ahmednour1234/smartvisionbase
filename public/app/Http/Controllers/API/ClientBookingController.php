<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Booking\BookingResource;
use App\Services\SponsorScheduleService;
use Illuminate\Http\Request;

class ClientBookingController extends Controller
{
    public function __construct(private SponsorScheduleService $service) {}

    /**
     * GET /api/client/bookings?status=pending&date=2025-11-05&time_from=10:00&time_to=12:00
     */
    public function index(Request $request)
    {
        // الجارد المناسب لعملائك
        $client = $request->user('client');
        if (!$client) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // فلاتر مدعومة
        $filters = $request->only(['date', 'status', 'time_from', 'time_to']);

        // جلب النتائج بصفحات
        $paginator = $this->service->clientBookings($client->id, $filters);

        // تحميل العلاقات على الـ Collection داخل الـ Paginator (مش على الـ Paginator نفسه)
        $paginator->getCollection()->load(['sponsor', 'client']);

        // إرجاع البيانات + بلوك pagination بالشكل المطلوب
        return BookingResource::collection($paginator)->additional([
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
                'has_more'     => $paginator->hasMorePages(),
            ],
        ]);
    }
}
