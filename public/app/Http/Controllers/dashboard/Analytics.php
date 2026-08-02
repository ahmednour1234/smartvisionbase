<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Client;
use App\Models\Event;
use App\Models\EventSchedule;
use Illuminate\Http\Request;
use App\Models\Pdf;
use App\Models\Project;
use App\Models\Role;
use App\Models\Speaker;
use App\Models\Sponsor;
use App\Models\User;
use App\Repositories\BlogRepository;
use Carbon\Carbon;


class Analytics extends Controller
{

public function index()
{
    $eventsCount     = Event::count();
    $speackerscount  = Speaker::count();
    $sponsorscount   = Sponsor::count();
    $usercount       = User::count();
    $blogcount       = Blog::whereDate('created_at', Carbon::today())->count();
    $scheduletoday   = EventSchedule::whereDate('start_datetime', Carbon::today())->count();
$number_register_today= Client::where('type',1)->whereDate('created_at', Carbon::today())->count();
$number_become_sponsor_today= Client::where('type',2)->whereDate('created_at', Carbon::today())->count();
$latest_register = Client::where('type', 1)
    ->latest('created_at')
    ->take(10)
    ->get();

$latest_become_sponsor = Client::where('type', 2)
    ->latest('created_at')
    ->take(10)
    ->get();

// أول فعالية قادمة
    $nextEvent = Event::whereDate('event_date', '>=', now())->orderBy('event_date')->first();
$eventDate = \App\Models\Event::where('event_date', '>', now())
    ->orderBy('event_date', 'asc')
    ->value('event_date'); // هذا يُرجع التاريخ فقط

    return view('content.dashboard.dashboards-analytics', compact(
        'eventsCount',
        'speackerscount',
        'sponsorscount',
        'usercount',
        'blogcount',
        'scheduletoday',
        'eventDate',
        'number_register_today',
        'number_become_sponsor_today',
        'latest_register',
        'latest_become_sponsor'
    ));
}

}
