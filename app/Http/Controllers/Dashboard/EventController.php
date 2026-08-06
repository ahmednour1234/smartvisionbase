<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::query()->orderBy('start_at', 'desc')->paginate(15);
        return view('dashboard.events.index', compact('events'));
    }

    public function create()
    {
        return view('dashboard.events.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ar' => ['required','string','max:255'],
            'name_en' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255', Rule::unique('events','slug')],
            'image' => ['nullable','image','max:2048'],
            'cover_image' => ['nullable','image','max:4096'],
            'row_image' => ['nullable','image','max:4096'],
            'start_at' => ['nullable','date'],
            'end_at' => ['nullable','date','after_or_equal:start_at'],
            'website_url_ar' => ['nullable','url','max:255'],
            'website_url_en' => ['nullable','url','max:255'],
            'is_active' => ['nullable','boolean'],
            'is_featured' => ['nullable','boolean'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name_en']);
        }
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['is_featured'] = (bool) ($data['is_featured'] ?? false);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = uniqid('event_') . '.' . $file->getClientOriginalExtension();
            $path = public_path('uploads/events');
            if (! is_dir($path)) {
                @mkdir($path, 0775, true);
            }
            $file->move($path, $filename);
            $data['image'] = 'uploads/events/' . $filename;
        }
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = uniqid('event_cover_') . '.' . $file->getClientOriginalExtension();
            $path = public_path('uploads/events');
            if (! is_dir($path)) {
                @mkdir($path, 0775, true);
            }
            $file->move($path, $filename);
            $data['cover_image'] = 'uploads/events/' . $filename;
        }
        if ($request->hasFile('row_image')) {
            $file = $request->file('row_image');
            $filename = uniqid('event_row_') . '.' . $file->getClientOriginalExtension();
            $path = public_path('uploads/events');
            if (! is_dir($path)) {
                @mkdir($path, 0775, true);
            }
            $file->move($path, $filename);
            $data['row_image'] = 'uploads/events/' . $filename;
        }

        Event::create($data);

        return redirect()->route('dashboard.events.index')->with('success', 'Event created successfully.');
    }

    public function edit(Event $event)
    {
        return view('dashboard.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'name_ar' => ['required','string','max:255'],
            'name_en' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255', Rule::unique('events','slug')->ignore($event->id)],
            'image' => ['nullable','image','max:2048'],
            'cover_image' => ['nullable','image','max:4096'],
            'row_image' => ['nullable','image','max:4096'],
            'start_at' => ['nullable','date'],
            'end_at' => ['nullable','date','after_or_equal:start_at'],
            'website_url_ar' => ['nullable','url','max:255'],
            'website_url_en' => ['nullable','url','max:255'],
            'is_active' => ['nullable','boolean'],
            'is_featured' => ['nullable','boolean'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name_en']);
        }
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['is_featured'] = (bool) ($data['is_featured'] ?? false);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = uniqid('event_') . '.' . $file->getClientOriginalExtension();
            $path = public_path('uploads/events');
            if (! is_dir($path)) {
                @mkdir($path, 0775, true);
            }
            $file->move($path, $filename);
            $data['image'] = 'uploads/events/' . $filename;
        }
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = uniqid('event_cover_') . '.' . $file->getClientOriginalExtension();
            $path = public_path('uploads/events');
            if (! is_dir($path)) {
                @mkdir($path, 0775, true);
            }
            $file->move($path, $filename);
            $data['cover_image'] = 'uploads/events/' . $filename;
        }
        if ($request->hasFile('row_image')) {
            $file = $request->file('row_image');
            $filename = uniqid('event_row_') . '.' . $file->getClientOriginalExtension();
            $path = public_path('uploads/events');
            if (! is_dir($path)) {
                @mkdir($path, 0775, true);
            }
            $file->move($path, $filename);
            $data['row_image'] = 'uploads/events/' . $filename;
        }

        $event->update($data);

        return redirect()->route('dashboard.events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('dashboard.events.index')->with('success', 'Event deleted.');
    }

    public function toggleActive(Event $event)
    {
        $event->is_active = ! $event->is_active;
        $event->save();
        return back()->with('success', 'Event status updated.');
    }

    public function toggleFeatured(Event $event)
    {
        $event->is_featured = ! $event->is_featured;
        $event->save();
        return back()->with('success', 'Event featured flag updated.');
    }
}


