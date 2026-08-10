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

class SchduleController extends Controller
{
   public function index()
{
    $schedule_section = HomeSection::where('is_active', true)->where('id', 5)->first();
    $event           = Event::first();
 $eventDays = [];
    $schedules = collect();

    if ($event) {
        $schedules = DB::table('event_schedules')
            ->where('event_id', $event->id)
            ->orderBy('start_datetime')
            ->get();

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
    ));
}
}
