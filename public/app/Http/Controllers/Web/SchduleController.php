<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\HomeSection;
use App\Models\Speaker;
use App\Models\Sponsor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\EventSchedule;

class SchduleController extends Controller
{
public function index()
{
    $schedule_section = HomeSection::where('is_active', true)->where('id', 5)->first();
    $event            = Event::first();
    $speakers         = Speaker::where('active', true)->paginate(6);

    $schedules  = collect();
    $eventDays  = [];

    if ($event) {
        // استعلام نظيف بدون أي concat/fallback
        $schedules = EventSchedule::query()
            ->with(['speakers:id,name_en'])   // eager load فقط، لا يسبب تكرار
            ->where('event_id', $event->id)
            ->select('event_schedules.*')     // مهم لو حصل join في مكان آخر
            ->orderBy('start_datetime')
            ->get()
            ->unique('id')                    // إزالة أي تكرار احتياطيًا
            ->values();

        // بناء الأيام للـ Tabs (بدون ما نكرّر)
        $eventDays = $schedules
            ->groupBy(fn ($s) => \Carbon\Carbon::parse($s->start_datetime)->toDateString())
            ->keys()
            ->sort()
            ->mapWithKeys(fn ($date) => [
                $date => \Carbon\Carbon::parse($date)->translatedFormat('j F Y'),
            ])
            ->toArray();
    }

    return view('web.content.schdule', compact(
        'schedule_section',
        'event',
        'schedules',
        'eventDays',
        'speakers'
    ));
}

}
