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

class HomeController extends Controller
{
   public function index()
{
    $home_slider     = HomeSection::where('is_active', true)->where('id', 1)->first();
    $promo_section   = HomeSection::where('is_active', true)->where('id', 2)->first();
    $about_section   = HomeSection::where('is_active', true)->where('id', 3)->first();
    $speaker_section = HomeSection::where('is_active', true)->where('id', 4)->first();
    $schedule_section = HomeSection::where('is_active', true)->where('id', 5)->first();
    $gallery_section = HomeSection::where('is_active', true)->where('id', 6)->first();
    $sponsor_section = HomeSection::where('is_active', true)->where('id', 7)->first();
    $blog_section = HomeSection::where('is_active', true)->where('id', 8)->first();
        $promo2 = HomeSection::where('is_active', true)->where('id', 9)->first();

    $sections = HomeSection::where('is_active', true)
    ->whereIn('id', [1,2,3,4,5,6,7,8,9])
    ->orderBy('section_order')
    ->get();
    $event           = Event::first();
    $speakers        = Speaker::where('active', true)->orderby('orders','asc')->paginate(6);

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
        ->take(2)
        ->flatMap(function ($group) {
            return $group->take(3);
        });

    if ($rawSchedules->count() < 4) {
        $fallback = EventSchedule::with(['speakers:id,name_en'])
            ->where('event_id', $event->id)
            ->orderBy('start_datetime')
            ->take(4 - $rawSchedules->count())
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
    $gallieries = Gallery::where('active', true)
        ->orderBy('created_at', 'desc')
        ->get();
        $sponsors=Sponsor::join('sponsor_categories as sc', 'sc.id', '=', 'sponsors.category_sponsor_id')
                ->orderBy('sc.orders', 'asc')        // ترتيب حسب التصنيف
                ->orderBy('sponsors.sort', 'asc')  // ثم داخل التصنيف
                ->orderBy('sponsors.created_at', 'asc')
                ->limit(100)
                ->get();
   
        $blogs=Blog::where('active', true)
        ->orderBy('created_at', 'desc')
        ->paginate(3);

    return view('web.content.home', compact(
        'home_slider',
            'sections',

        'promo_section',
        'about_section',
        'event',
        'speakers',
        'speaker_section',
        'eventDays',
        'schedules',
        'schedule_section',
        'gallieries',
        'gallery_section',
        'sponsor_section',
        'sponsors',
        'blogs',
        'blog_section',
        'promo2'
    ));
}
}
