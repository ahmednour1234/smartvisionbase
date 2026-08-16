<?php
// app/Http/Controllers/Dashboard/SpeakerTimeController.php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\{Speaker, SpeakerTime, Booking};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SpeakerTimeController extends Controller
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

        $q = SpeakerTime::with('speaker')
            ->when($filters['speaker_id'], fn($qq)=>$qq->where('speaker_id', $filters['speaker_id']))
            ->when($filters['date_from'], fn($qq)=>$qq->whereDate('date','>=',$filters['date_from']))
            ->when($filters['date_to'],   fn($qq)=>$qq->whereDate('date','<=',$filters['date_to']))
            ->when($filters['time_from'], fn($qq)=>$qq->where('time_to','>',  $filters['time_from']))
            ->when($filters['time_to'],   fn($qq)=>$qq->where('time_from','<',$filters['time_to']))
            ->when($filters['status'],    fn($qq)=>$qq->where('status',$filters['status']));

        if (!is_null($filters['active'])) {
            $q->where('active', $filters['active'] ? 1 : 0);
        }

        $times = $q->orderBy('date')->orderBy('time_from')->paginate(12)->appends($request->query());

        $speakers = Speaker::where('type',1)->orderBy('id','desc')->get(['id','name_ar','name_en']);

        return view('content.speaker_times.index', compact('times','filters','speakers'));
    }

    public function create(Request $request)
    {
        $speakers = Speaker::where('type',1)->orderBy('id','desc')->get(['id','name_ar','name_en']);
        return view('content.speaker_times.create', compact('speakers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'speaker_id' => ['required','exists:speakers,id'],
            'date'       => ['required','date'],
            'time_from'  => ['required','date_format:H:i'],
            'time_to'    => ['required','date_format:H:i','after:time_from'],
            'status'     => ['required', Rule::in(['available','booked','unavailable'])],
            'active'     => ['nullable','boolean'],
        ]);

        // منع التداخل لنفس المتحدث/اليوم
        $overlap = SpeakerTime::where('speaker_id',$data['speaker_id'])
            ->whereDate('date',$data['date'])
            ->where('time_from','<',$data['time_to'])
            ->where('time_to','>',$data['time_from'])
            ->exists();
        if ($overlap) {
            return back()->withErrors(['time_from'=>'يوجد تداخل مع فتحة زمنية أخرى'])->withInput();
        }

        SpeakerTime::create($data + ['active'=>$request->boolean('active',true)]);

        return redirect()->route('dashboard.speaker-times.index')->with('success','تم إنشاء الموعد');
    }

    public function show(SpeakerTime $time)
    {
        $time->load(['speaker','bookings'=>fn($q)=>$q->orderBy('time_from')]);
        return view('content.speaker_times.show', compact('time'));
    }

    public function edit(SpeakerTime $time)
    {
        $speakers = Speaker::where('type',1)->orderBy('id','desc')->get(['id','name_ar','name_en']);
        return view('content.speaker_times.edit', compact('time','speakers'));
    }

    public function update(Request $request, SpeakerTime $time)
    {
        $data = $request->validate([
            'speaker_id' => ['required','exists:speakers,id'],
            'date'       => ['required','date'],
            'time_from'  => ['required','date_format:H:i'],
            'time_to'    => ['required','date_format:H:i','after:time_from'],
            'status'     => ['required', Rule::in(['available','booked','unavailable'])],
            'active'     => ['nullable','boolean'],
        ]);

        $overlap = SpeakerTime::where('speaker_id',$data['speaker_id'])
            ->whereDate('date',$data['date'])
            ->where('id','<>',$time->id)
            ->where('time_from','<',$data['time_to'])
            ->where('time_to','>',$data['time_from'])
            ->exists();
        if ($overlap) {
            return back()->withErrors(['time_from'=>'يوجد تداخل مع فتحة زمنية أخرى'])->withInput();
        }

        $time->update($data + ['active'=>$request->boolean('active',true)]);

        return redirect()->route('dashboard.speaker-times.index')->with('success','تم التحديث');
    }

    public function toggleActive(SpeakerTime $time)
    {
        $time->update(['active' => !$time->active]);
        return back()->with('success','تم تغيير حالة التفعيل');
    }

    public function destroy(SpeakerTime $time)
    {
        $time->delete();
        return back()->with('success','تم الحذف');
    }
}
