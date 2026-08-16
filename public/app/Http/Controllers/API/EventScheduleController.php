<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Event\EventScheduleDayCollection;
use App\Http\Resources\Event\EventScheduleResource;
use App\Models\Event;
use App\Models\EventSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EventScheduleController extends Controller
{
    /**
     * GET /api/events/{event}/schedule
     * يرجّع أيام الحدث (max 4 افتراضيًا) وكل يوم تحته حتى 4 جلسات (افتراضيًا)، مع المتحدثين.
     */
    public function index(Request $request, Event $event)
    {
        // حدود قابلة للتعديل من الكويري
        $daysLimit   = (int) $request->query('days', 4);
        $daysLimit   = max(1, min($daysLimit, 7));
        $perDayLimit = (int) $request->query('per_day', 4);
        $perDayLimit = max(1, min($perDayLimit, 10));

        $ttl = now()->addMinutes(1);

        // 1) استخرج الأيام (Distinct DATE) بحد أقصى $daysLimit
        $days = Cache::remember("home:event:{$event->id}:days", $ttl, function () use ($event, $daysLimit) {
            return EventSchedule::query()
                ->where('event_id', $event->id)
                ->orderBy('start_datetime')
                ->selectRaw('DATE(start_datetime) as d')
                ->distinct()
                ->limit($daysLimit)
                ->pluck('d'); // collection of 'Y-m-d'
        });

        $dayGroups = collect();

        if ($days->isNotEmpty()) {
            // 2) أحضر كل الجلسات لكل الأيام المحدّدة (مع المتحدثين)
            $cacheKey = "home:event:{$event->id}:schedules:" . md5($days->join(','));
            $allSchedules = Cache::remember($cacheKey, $ttl, function () use ($event, $days) {
                return EventSchedule::query()
                    ->with(['speakers:id,name_en,name_ar,image'])
                    ->where('event_id', $event->id)
                    ->whereIn(DB::raw('DATE(start_datetime)'), $days->all())
                    ->orderBy('start_datetime')
                    ->get();
            });

            // 3) Group by day + limit per day
            $grouped = $allSchedules
                ->groupBy(fn ($s) => \Carbon\Carbon::parse($s->start_datetime)->toDateString())
                ->map(fn ($items) => $items->take($perDayLimit)->values());

            // 4) كوّن مصفوفة الأيام بالترتيب المطلوب
            foreach ($days as $d) {
                $dayGroups->push([
                    'date'      => $d,                              // 'Y-m-d'
                    'schedules' => $grouped->get($d, collect()),   // Collection<EventSchedule>
                ]);
            }
        }

        // نستخدم Collection Resource لعرض الأيام
        return new EventScheduleDayCollection($dayGroups);
    }

    /**
     * GET /api/event-schedule/{schedule}
     * عرض جلسة واحدة + المتحدثين
     */
    public function show(Request $request, EventSchedule $schedule)
    {
        $schedule->load(['speakers:id,name_en,name_ar,image']);
        return new EventScheduleResource($schedule);
    }
}
