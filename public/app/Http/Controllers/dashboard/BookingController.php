<?php
// app/Http/Controllers/Dashboard/BookingController.php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\{Speaker, Client, SpeakerTime, Booking};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        // فلاتر: speaker_id، date_from، date_to، time_from، time_to، status، active
        $filters = [
            'speaker_id' => $request->query('speaker_id'),
            'date_from'  => $request->query('date_from'),
            'date_to'    => $request->query('date_to'),
            'time_from'  => $request->query('time_from'), // HH:MM
            'time_to'    => $request->query('time_to'),   // HH:MM
            'status'     => $request->query('status'),
            'active'     => $request->boolean('active', null),
        ];

        $q = Booking::with(['speaker','client','speakerTime'])
            ->when($filters['speaker_id'], fn($qq)=>$qq->where('speaker_id',$filters['speaker_id']))
            ->when($filters['date_from'],  fn($qq)=>$qq->whereDate('time_from','>=',$filters['date_from']))
            ->when($filters['date_to'],    fn($qq)=>$qq->whereDate('time_from','<=',$filters['date_to']))
            ->when($filters['status'],     fn($qq)=>$qq->where('status',$filters['status']));

        if ($filters['time_from']) {
            $q->whereRaw('TIME(time_to) > ?', [$filters['time_from']]);
        }
        if ($filters['time_to']) {
            $q->whereRaw('TIME(time_from) < ?', [$filters['time_to']]);
        }
        if (!is_null($filters['active'])) {
            $q->where('active', $filters['active'] ? 1 : 0);
        }

        $bookings = $q->orderByDesc('time_from')->paginate(12)->appends($request->query());

        $speakers = Speaker::where('type',1)->orderBy('id','desc')->get(['id','name_ar','name_en']);

        return view('content.bookings.index', compact('bookings','filters','speakers'));
    }

    public function create(Request $request)
    {
        $speakers = Speaker::where('type',1)->orderBy('id','desc')->get(['id','name_ar','name_en']);
        $clients  = Client::orderBy('id','desc')->get(['id','name']); // عدّل الحقول حسب جدولك
        $speakerTimes = collect();

        if ($request->filled('speaker_id')) {
            $speakerTimes = SpeakerTime::where('speaker_id',$request->query('speaker_id'))
                ->where('status','!=','unavailable')
                ->orderBy('date')->orderBy('time_from')->get();
        }

        return view('content.bookings.create', compact('speakers','clients','speakerTimes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'speaker_id'      => ['required','exists:speakers,id'],
            'client_id'       => ['required','exists:clients,id'],
            'speaker_time_id' => ['nullable','exists:speaker_times,id'],
            'time_from'       => ['required','date'],
            'time_to'         => ['required','date','after:time_from'],
            'status'          => ['required', Rule::in(['pending','confirmed','cancelled'])],
            'active'          => ['nullable','boolean'],
        ]);

        // لو مربوط بفتحة زمنية، تأكد أن الموعد داخل نطاقها
        if (!empty($data['speaker_time_id'])) {
            $slot = SpeakerTime::findOrFail($data['speaker_time_id']);
            if ((int)$slot->speaker_id !== (int)$data['speaker_id']) {
                return back()->withErrors(['speaker_time_id'=>'الفتحة لا تخص المتحدث المحدد'])->withInput();
            }
            $slotStart = Carbon::parse($slot->date.' '.$slot->time_from);
            $slotEnd   = Carbon::parse($slot->date.' '.$slot->time_to);
            if (!(Carbon::parse($data['time_from'])->betweenIncluded($slotStart,$slotEnd) &&
                  Carbon::parse($data['time_to'])->betweenIncluded($slotStart,$slotEnd))) {
                return back()->withErrors(['time_from'=>'موعد الحجز خارج نطاق الفتحة الزمنية'])->withInput();
            }
        }

        // منع التعارض لحجوزات نفس المتحدث
        $overlap = Booking::where('speaker_id',$data['speaker_id'])
            ->where('time_from','<',$data['time_to'])
            ->where('time_to','>',$data['time_from'])
            ->exists();
        if ($overlap) {
            return back()->withErrors(['time_from'=>'يوجد تعارض مع حجز آخر لنفس المتحدث'])->withInput();
        }

        Booking::create($data + ['active'=>$request->boolean('active',true)]);

        return redirect()->route('dashboard.bookings.index')->with('success','تم إنشاء الحجز');
    }

    public function show(Booking $booking)
    {
        $booking->load(['speaker','client','speakerTime']);
        return view('content.booking.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $speakers = Speaker::where('type',1)->orderBy('id','desc')->get(['id','name_ar','name_en']);
        $clients  = Client::orderBy('id','desc')->get(['id','name']);
        $speakerTimes = SpeakerTime::where('speaker_id',$booking->speaker_id)
            ->where('status','!=','unavailable')
            ->orderBy('date')->orderBy('time_from')->get();

        return view('content.bookings.edit', compact('booking','speakers','clients','speakerTimes'));
    }

    public function update(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'speaker_id'      => ['required','exists:speakers,id'],
            'client_id'       => ['required','exists:clients,id'],
            'speaker_time_id' => ['nullable','exists:speaker_times,id'],
            'time_from'       => ['required','date'],
            'time_to'         => ['required','date','after:time_from'],
            'status'          => ['required', Rule::in(['pending','confirmed','cancelled'])],
            'active'          => ['nullable','boolean'],
        ]);

        if (!empty($data['speaker_time_id'])) {
            $slot = SpeakerTime::findOrFail($data['speaker_time_id']);
            if ((int)$slot->speaker_id !== (int)$data['speaker_id']) {
                return back()->withErrors(['speaker_time_id'=>'الفتحة لا تخص المتحدث المحدد'])->withInput();
            }
            $slotStart = Carbon::parse($slot->date.' '.$slot->time_from);
            $slotEnd   = Carbon::parse($slot->date.' '.$slot->time_to);
            if (!(Carbon::parse($data['time_from'])->betweenIncluded($slotStart,$slotEnd) &&
                  Carbon::parse($data['time_to'])->betweenIncluded($slotStart,$slotEnd))) {
                return back()->withErrors(['time_from'=>'موعد الحجز خارج نطاق الفتحة الزمنية'])->withInput();
            }
        }

        $overlap = Booking::where('speaker_id',$data['speaker_id'])
            ->where('id','<>',$booking->id)
            ->where('time_from','<',$data['time_to'])
            ->where('time_to','>',$data['time_from'])
            ->exists();
        if ($overlap) {
            return back()->withErrors(['time_from'=>'يوجد تعارض مع حجز آخر لنفس المتحدث'])->withInput();
        }

        $booking->update($data + ['active'=>$request->boolean('active',true)]);
        return redirect()->route('dashboard.bookings.index')->with('success','تم التحديث');
    }

    public function toggleActive(Booking $booking)
    {
        $booking->update(['active' => !$booking->active]);
        return back()->with('success','تم تغيير حالة التفعيل');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return back()->with('success','تم الحذف');
    }
}
