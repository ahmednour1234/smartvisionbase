<?php
namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Helpers\FileHelper;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::latest()->paginate(10);
        return view('content.events.index', compact('events'));
    }

    public function create()
    {
        return view('content.events.create');
    }

    public function store(EventRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('main_image')) {
    $data['main_image'] = FileHelper::uploadImage($request->file('main_image'), 'events');
}


        Event::create($data);

        return redirect()->route('admin.events.index')->with('success', __('event.created'));
    }

    public function edit(Event $event)
    {
        return view('content.events.edit', compact('event'));
    }

    public function update(EventRequest $request, Event $event)
    {
        $data = $request->validated();

        if ($request->hasFile('main_image')) {
            $path = $request->file('main_image')->store('events', 'public');
            $data['main_image'] = $path;
        }

        $event->update($data);

        return redirect()->route('admin.events.index')->with('success', __('event.updated'));
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', __('event.deleted'));
    }
}
