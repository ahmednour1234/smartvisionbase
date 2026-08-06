<?php

namespace App\Http\Controllers;

use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::active()
            ->where('start_at', '>=', now())
            ->orderBy('start_at')
            ->paginate(9);

        return view('site.events.index', compact('events'));
    }

    public function show(string $slug)
    {
        $event = Event::active()->where('slug', $slug)->firstOrFail();
        return view('site.events.show', compact('event'));
    }
}
