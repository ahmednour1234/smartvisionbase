<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\HomeSection;
use App\Models\Speaker;
use App\Models\Prize;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use App\Models\Sponsor;
use App\Models\Company;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
   public function index()
{
    $aboutSection   = HomeSection::where('is_active', true)->where('id', 3)->first();
    $gallery_section = HomeSection::where('is_active', true)->where('id', 6)->first();
    $sponsor_section = HomeSection::where('is_active', true)->where('id', 7)->first();
    $sponsor_section = HomeSection::where('is_active', true)->where('id', 7)->first();
    $speaker_section = HomeSection::where('is_active', true)->where('id', 4)->first();
    $speakers        = Speaker::where('active', true)->paginate(6);
    $schedule_section = HomeSection::where('is_active', true)->where('id', 5)->first();
    $event           = Event::first();
        $companies = Company::where('active', 1)->get();
$prizes=Prize::where('active',1)->take(4)->get();
  $sponsors=Sponsor::where('active', true)
        ->orderBy('created_at', 'desc')
        ->get();

    $gallieries = Gallery::where('active', true)
        ->orderBy('created_at', 'desc')
        ->get();
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
    return view('web.content.contact', compact(
        'aboutSection',
        'gallieries',
        'gallery_section',
        'sponsor_section',
        'sponsors',
        'speaker_section',
        'speakers',
        'schedule_section',
        'event',
        'schedules',
        'eventDays',
        'companies',
        'prizes'
    ));
}
public function store(Request $request)
{
    $data = $request->validate([
        'name'    => 'required|string|max:255',
        'title'   => 'nullable|string|max:255',
        'email'   => 'required|email|max:255',
        'phone'   => 'nullable|string|max:50',
        'message' => 'required|string',
    ]);

    ContactUs::create($data);

    return redirect()->back()->with('success', 'Your message has been sent successfully.');
}

}
