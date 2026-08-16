<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Event\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class EventController extends Controller
{
    /**
     * GET /api/home/event
     * يرجّع الحدث الحالي مع كاش لمدة دقيقة.
     * المنطق:
     *  - يبحث عن أقرب حدث نشِط وتاريخه >= اليوم (تصاعدي)
     *  - إن لم يوجد: آخر حدث نشِط بحسب end_date تنازليًا، ثم id تنازليًا
     */
    public function current(Request $request)
    {
        $ttl = now()->addMinutes(1);

        $event = Cache::remember('home:event', $ttl, function () {
            $today = Carbon::today();

            $upcoming = Event::query()
                ->where('active', 1)
                ->whereDate('event_date', '>=', $today)
                ->orderBy('event_date', 'asc')
                ->orderBy('id', 'asc')
                ->first();

            if ($upcoming) return $upcoming;

            return Event::query()
                ->where('active', 1)
                ->orderByDesc('end_date')
                ->orderByDesc('id')
                ->first();
        });

        if (!$event) {
            return response()->json(['message' => 'No event found', 'data' => null], 404);
        }

        return new EventResource($event);
    }

    /**
     * GET /api/events/{event}
     * يعرض حدثًا واحدًا (يرفض غير النشِط بـ 404)
     */
    public function show(Request $request, Event $event)
    {
        if ((int) $event->active !== 1) {
            abort(404);
        }

        return new EventResource($event);
    }
}
