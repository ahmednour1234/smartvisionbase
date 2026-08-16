<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\HomeSection;
use App\Models\Speaker;
use App\Models\Voting;
use App\Models\Sponsor;
use App\Models\Company;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\Setting;

class VotingController extends Controller
{
public function index()
{
    $aboutSection     = HomeSection::where('is_active', true)->where('id', 3)->first();
    $gallery_section  = HomeSection::where('is_active', true)->where('id', 6)->first();
    $sponsor_section  = HomeSection::where('is_active', true)->where('id', 7)->first();
    $speaker_section  = HomeSection::where('is_active', true)->where('id', 4)->first();
    $speakers         = Speaker::where('active', true)->paginate(6);

    $event            = Event::first();

    // مؤثرين للتصويت (مرتبين)
    $companies = Company::where('active', 1)
        ->orderBy('orders', 'asc')
        ->get();

    $sponsors = Sponsor::where('active', true)
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
            $date = \Carbon\Carbon::parse($schedule->start_datetime)->format('Y-m-d');
            $eventDays[$date] = \Carbon\Carbon::parse($schedule->start_datetime)->translatedFormat('j F Y');
        }
        ksort($eventDays);
    }

    return view('web.content.voting', compact(
        'aboutSection',
        'gallieries',
        'gallery_section',
        'sponsor_section',
        'sponsors',
        'speaker_section',
        'speakers',
        'event',
        'schedules',
        'eventDays',
        'companies',
    ));
}

public function show($name_en)
{
        $aboutSection   = HomeSection::where('is_active', true)->where('id', 3)->first();

    $company = Company::where('active', 1)->where('name_en',$name_en)->first();

    return view('web.content.company_details', compact('company','aboutSection'));
}
public function vote($id)
{
    // 0) Check global voting toggle from settings table
    $votingEnabled = (int) (Setting::query()->value('voting') ?? 0);
    if ($votingEnabled !== 1) {
        return response()->json([
            'message' => 'Voting is now closed. Thank you.'
        ], 403); // Forbidden (closed)
    }

    // 1) Company & IP
    $company = Company::findOrFail($id);
    $ip = request()->ip();

    // 2) Throttle by IP (30 minutes)
    $recentVote = Voting::where('company_id', $company->id)
        ->where('ip_address', $ip)
        ->where('created_at', '>=', now()->subMinutes(30))
        ->first();

    if ($recentVote) {
        return response()->json([
            'message' => 'You have already voted for this Influncer .'
        ], 429);
    }

    // 3) Create vote
    Voting::create([
        'company_id' => $company->id,
        'ip_address' => $ip,
    ]);

    $company->increment('count_vote');

    return response()->json([
        'message' => 'Thanks for voting for ' . $company->name_en . '!'
    ]);
}
public function loadMore(Request $request)
{
    $perPage = (int) $request->integer('per_page', 25);
    $page    = (int) $request->integer('page', 2);

    $paginator = \App\Models\Company::where('active', 1)
        ->orderBy('orders', 'asc')
        ->paginate($perPage, ['*'], 'page', $page);

    // نرجّع نفس البارتشالز بتوعك (لازم يطبعوا “الكروت” بس من غير حاويات خارجية)
    $desktopHtml = view('web.content.partials.voting-desktop', [
        'companies' => $paginator->items(),
    ])->render();

    $mobileHtml = view('web.content.partials.voting-mobile', [
        'companies' => $paginator->items(),
    ])->render();

    return response()->json([
        'desktop' => $desktopHtml,
        'mobile'  => $mobileHtml,
        'hasMore' => $paginator->hasMorePages(),
        'nextPage'=> $page + 1,
    ]);
}

}
