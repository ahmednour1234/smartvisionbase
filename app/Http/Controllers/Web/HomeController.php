<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\HomeSection;
use App\Models\Speaker;
use App\Models\Sponsor;
use App\Models\Company;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
        $home_sections = HomeSection::where('is_active', true)
        ->orderBy('section_order')
        ->get()
        ->keyBy('id');
                $perPage   = 25; // عدّلها حسب تصميمك

$companies = Company::where('active', 1)  ->orderBy('orders', 'asc')
    ->paginate($perPage);

    $event           = Event::first();
    $speakers        = Speaker::where('active', true)->paginate(6);

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
    $gallieries = Gallery::where('active', true)
        ->orderBy('created_at', 'desc')
        ->get();
        $sponsors=Sponsor::where('active', true)
        ->orderBy('created_at', 'desc')
        ->get();
        $blogs=Blog::where('active', true)
        ->orderBy('created_at', 'desc')
        ->paginate(3);

    return view('web.content.home', compact(
        'home_slider',
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
        'companies',
        'home_sections',
        'perPage'
    ));
}
}
