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
    $event           = Event::first();
        $speakers        = Speaker::where('active', true)->paginate(6);
 $eventDays = [];
    $schedules = collect();

   if ($event) {
    // نستخدم Eloquent مع العلاقة
    $rawSchedules = EventSchedule::with(['speakers:id,name_en'])
        ->where('event_id', $event->id)
        ->orderBy('start_datetime')
        ->get()
        ->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->start_datetime)->format('Y-m-d');
        })
        ->take(600)
        ->flatMap(function ($group) {
            return $group->take(600);
        });

    if ($rawSchedules->count() < 600) {
        $fallback = EventSchedule::with(['speakers:id,name_en'])
            ->where('event_id', $event->id)
            ->orderBy('start_datetime')
            ->take(5 - $rawSchedules->count())
            ->get();

        $schedules = $rawSchedules->concat($fallback);
    } else {
        $schedules = $rawSchedules;
    }

    foreach ($schedules as $schedule) {
        $date = Carbon::parse($schedule->start_datetime)->format('Y-m-d');
        $eventDays[$date] = Carbon::parse($schedule->start_datetime)->translatedFormat('j F Y');
    }

    ksort($eventDays);
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
