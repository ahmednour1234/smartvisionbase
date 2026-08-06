<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventScheduleRequest;
use App\Models\EventSchedule;
use App\Repositories\EventScheduleRepository;
use Illuminate\Http\Request;
use App\Models\Speaker;
use App\Models\Event;
use App\Helpers\FileHelper;

class EventScheduleController extends Controller
{
    protected $repo;

    public function __construct(EventScheduleRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $schedules = $this->repo->filter($request);
        return view('content.event_schedules.index', compact('schedules'));
    }

    public function create()
    {
        $events = Event::all();
        $speakers = Speaker::where('active', true)->get();
        return view('content.event_schedules.create', compact('events', 'speakers'));
    }

    public function store(EventScheduleRequest $request)
    {
        $data = $request->validated();

        // رفع الصورة إذا كانت موجودة
        if ($request->hasFile('logo')) {
            $data['logo'] = FileHelper::uploadImage($request->file('logo'), 'uploads/event_schedules');
        }

        $this->repo->create($data);

        return redirect()->route('admin.event_schedules.index')
                         ->with('success', __('event_schedule.created'));
    }

    public function show(EventSchedule $eventSchedule)
    {
        return view('content.event_schedules.show', compact('eventSchedule'));
    }

    public function edit(EventSchedule $eventSchedule)
    {
        $events = Event::all();
        $speakers = Speaker::all();
        return view('content.event_schedules.edit', compact('eventSchedule', 'events', 'speakers'));
    }

    public function update(EventScheduleRequest $request, EventSchedule $eventSchedule)
    {
        $data = $request->validated();

        // رفع صورة جديدة إذا تم إدخالها
        if ($request->hasFile('logo')) {
            $data['logo'] = FileHelper::uploadImage($request->file('logo'), 'uploads/event_schedules');
        }

        $this->repo->update($eventSchedule, $data);

        return redirect()->route('admin.event_schedules.index')
                         ->with('success', __('event_schedule.updated'));
    }

    public function destroy(EventSchedule $eventSchedule)
    {
        $this->repo->delete($eventSchedule);
        return redirect()->route('admin.event_schedules.index')
                         ->with('success', __('event_schedule.deleted'));
    }

    // ✅ تعطيل الفعالية
public function cancel(EventSchedule $eventSchedule)
{
    $eventSchedule->update([
        'active' => !$eventSchedule->active,
    ]);

    return redirect()->route('admin.event_schedules.index')
                     ->with('success', $eventSchedule->active
                         ? __('event_schedule.activated')
                         : __('event_schedule.cancelled'));
}

}
