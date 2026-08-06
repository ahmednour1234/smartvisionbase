<?php

namespace App\Repositories;

use App\Models\EventSchedule;
use Illuminate\Http\Request;

class EventScheduleRepository
{
    public function all()
    {
        return EventSchedule::with('event', 'speakers')->latest()->get();
    }

    public function paginate($perPage = 10)
    {
        return EventSchedule::with('event', 'speakers')->latest()->paginate($perPage);
    }

    public function find($id)
    {
        return EventSchedule::with('event', 'speakers')->findOrFail($id);
    }

    public function create(array $data)
    {
        $schedule = EventSchedule::create($data);

        if (isset($data['speaker_ids'])) {
            $schedule->speakers()->sync($data['speaker_ids']);
        }

        return $schedule;
    }

    public function update(EventSchedule $schedule, array $data)
    {
        $schedule->update($data);

        if (isset($data['speaker_ids'])) {
            $schedule->speakers()->sync($data['speaker_ids']);
        }

        return $schedule;
    }

    public function delete(EventSchedule $schedule)
    {
        $schedule->speakers()->detach();
        return $schedule->delete();
    }
      public function filter(Request $request, $limit = 10)
    {
        $query = EventSchedule::query();

        if ($request->filled('event_id')) {
            $query->where('id', $request->event_id);
        }

        if ($request->filled('start_time')) {
            $query->where('start_time', '>=', $request->start_time);
        }

        if ($request->filled('end_time')) {
            $query->where('end_time', '<=', $request->end_time);
        }

        if ($request->filled('speaker_id')) {
            $query->whereHas('schedules.speakers', function ($q) use ($request) {
                $q->where('speakers.id', $request->speaker_id);
            });
        }

        return $query->latest()->paginate($limit);
    }
}
