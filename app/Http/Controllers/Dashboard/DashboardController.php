<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\ContactMessage;

class DashboardController extends Controller
{
    public function index()
    {
        $upcomingCount = Event::active()->where('start_at', '>', now())->count();
        $activeCount = Event::active()->count();
        $next = Event::active()->orderBy('start_at')->first();
        $lastMessages = ContactMessage::latest()->take(5)->get();

        return view('dashboard.index', compact(
            'upcomingCount','activeCount','next','lastMessages'
        ));
    }
}
