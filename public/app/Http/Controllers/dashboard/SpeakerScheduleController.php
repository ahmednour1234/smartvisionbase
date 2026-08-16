<?php
// app/Http/Controllers/Dashboard/SpeakerScheduleController.php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\{Speaker, SpeakerTime, Booking, Client};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SpeakerScheduleController extends Controller
{
    /** صفحة عرض المواعيد والحجوزات لسبيكر واحد */
    public function show(Request $request, Speaker $speaker)
    {
        $filters = [
            'date'   => $request->query('date'),       // يوم محدد (اختياري)
            'status' => $request->query('status'),     // حالة الفتحات available/booked/unavailable
        ];

        $times = SpeakerTime::withCount([
                'bookings as confirmed_bookings_count' => function ($q) {
                    $q->where('status', 'confirmed');
                }
            ])
            ->where('speaker_id', $speaker->id)
            ->when($filters['date'], fn($q) => $q->whereDate('date', $filters['date']))
            ->when($filters['status'], fn($q) => $q->where('status', $filters['status']))
            ->orderBy('date')->orderBy('time_from')
            ->get();

        // الحجوزات للمتابعة فقط
        $bookings = Booking::with(['client','speakerTime'])
            ->where('speaker_id', $speaker->id)
            ->when($filters['date'], fn($q) => $q->whereDate('time_from', $filters['date']))
            ->orderBy('time_from')
            ->get();

        return view('content.speakers.schedule.show', compact('speaker','times','bookings','filters'));
    }

    /** فورم إنشاء فتحة زمنية */
    public function createTime(Speaker $speaker)
    {
        return view('content.speakers.times.create', compact('speaker'));
    }

    /** حفظ فتحة زمنية جديدة */
    public function storeTime(Request $request, Speaker $speaker)
    {
        $data = $request->validate([
            'date'      => ['required','date'],
            'time_from' => ['required','date_format:H:i'],
            'time_to'   => ['required','date_format:H:i','after:time_from'],
            'status'    => ['nullable', Rule::in(['available','booked','unavailable'])],
            'active'    => ['nullable','boolean'],
        ]);

        // منع تداخل الأوقات لنفس اليوم والمتحدث
        $overlap = SpeakerTime::where('speaker_id', $speaker->id)
            ->whereDate('date', $data['date'])
            ->where(function($q) use ($data) {
                $q->where('time_from', '<', $data['time_to'])
                  ->where('time_to',   '>', $data['time_from']);
            })->exists();

        if ($overlap) {
            return back()->withErrors(['time_from' => 'يوجد تعارض مع فتحة زمنية أخرى لهذا المتحدث في نفس اليوم'])->withInput();
        }

        SpeakerTime::create([
            'speaker_id' => $speaker->id,
            'date'       => $data['date'],
            'time_from'  => $data['time_from'],
            'time_to'    => $data['time_to'],
            'status'     => $data['status'] ?? 'available',
            'active'     => $data['active'] ?? true,
        ]);

        return redirect()
            ->back()
            ->with('success', 'تم إنشاء الفتحة الزمنية بنجاح');
    }

    /** فورم تعديل فتحة زمنية */
    public function editTime(SpeakerTime $time)
    {
        $speaker = $time->speaker; // لسهولة العودة
        return view('content.speakers.times.edit', compact('time','speaker'));
    }

    /** تحديث فتحة زمنية */
    public function updateTime(Request $request, SpeakerTime $time)
    {
        $data = $request->validate([
            'date'      => ['required','date'],
            'time_from' => ['required','date_format:H:i'],
            'time_to'   => ['required','date_format:H:i','after:time_from'],
            'status'    => ['nullable', Rule::in(['available','booked','unavailable'])],
            'active'    => ['nullable','boolean'],
        ]);

        // منع التداخل مع غيرها لنفس اليوم والمتحدث
        $overlap = SpeakerTime::where('speaker_id', $time->speaker_id)
            ->whereDate('date', $data['date'])
            ->where('id', '<>', $time->id)
            ->where(function($q) use ($data) {
                $q->where('time_from', '<', $data['time_to'])
                  ->where('time_to',   '>', $data['time_from']);
            })->exists();

        if ($overlap) {
            return back()->withErrors(['time_from' => 'يوجد تعارض مع فتحة زمنية أخرى'])->withInput();
        }

        $time->update([
            'date'      => $data['date'],
            'time_from' => $data['time_from'],
            'time_to'   => $data['time_to'],
            'status'    => $data['status'] ?? $time->status,
            'active'    => $data['active'] ?? $time->active,
        ]);

        return redirect()
            ->route('dashboard.speakers.schedule', $time->speaker_id)
            ->with('success', 'تم تحديث الفتحة الزمنية');
    }

    /** تفعيل/تعطيل فتحة زمنية */
    public function toggleTimeActive(SpeakerTime $time)
    {
        $time->update(['active' => !$time->active]);

        return redirect()
            ->back()
            ->with('success', 'تم تغيير حالة التفعيل');
    }

    /** حذف (Soft Delete) */
    public function destroyTime(SpeakerTime $time)
    {
        $speakerId = $time->speaker_id;
        $time->delete();

        return redirect()
            ->back()
            ->with('success', 'تم حذف الفتحة الزمنية');
    }
}
