<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\HomeSection;
use App\Models\Speaker;
use App\Models\EventSchedule;
use Illuminate\Support\Carbon;

class SchduleController extends Controller
{
    public function index()
    {
        // سيكشن الأجندة (لو عندك ID ثابت = 5)
        $schedule_section = HomeSection::where('is_active', true)
            ->where('id', 5)
            ->first();

        // أول فعالية (ممكن تعدلها لـ where('active',1) حسب مشروعك)
        $event = Event::first();

        $speakers         = Speaker::where('active', true)->get();
        $groupedSchedules = collect();
        $eventDays        = [];

        if ($event) {
            // نجيب كل السجلات مرة واحدة فقط بدون fallback أو concat
            $schedules = EventSchedule::with([
                    'speakers:id,name_en,name_ar', // لو عندك name_ar
                ])
                ->where('event_id', $event->id)
                ->orderBy('start_datetime')
                ->get()
                ->unique('id'); // ضمان عدم تكرار السجلات لو الجدول فيه دuplicates

            // نجمع حسب اليوم
            $groupedSchedules = $schedules->groupBy(function ($item) {
                return Carbon::parse($item->start_datetime)->format('Y-m-d');
            });

            // تجهيز array بالأيام بشكل مقروء
            foreach ($groupedSchedules as $date => $daySchedules) {
                $eventDays[$date] = Carbon::parse($date)->translatedFormat('j F Y');
            }
        }

        return view('web.content.schdule', [
            'schedule_section' => $schedule_section,
            'event'            => $event,
            'groupedSchedules' => $groupedSchedules,
            'eventDays'        => $eventDays,
            'speakers'         => $speakers,
        ]);
    }
}
