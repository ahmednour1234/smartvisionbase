<?php
// app/Http/Controllers/Api/Client/ClientApiController.php

namespace App\Http\Controllers\API\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\{Speaker, SpeakerTime, Booking};
use App\Http\Resources\Speaker\SpeakerResource;
use App\Http\Resources\SpeakerTime\SpeakerTimeResource;
use App\Http\Resources\Booking\BookingResource;
use DB;
class ClientApiController extends Controller
{
    /** GET /api/client/speakers?search=&page= */
    public function listSpeakers(Request $request)
    {
        $q = Speaker::query()->where('type', 1);

        if ($s = $request->query('search')) {
            $q->where(function($qq) use ($s) {
                $qq->where('name_en','like',"%$s%")
                   ->orWhere('name_ar','like',"%$s%")
                   ->orWhere('title_en','like',"%$s%")
                   ->orWhere('title_ar','like',"%$s%");
            });
        }

        $speakers = $q->orderBy('id','desc')->paginate(15);
        return SpeakerResource::collection($speakers);
    }

    /** GET /api/client/speakers/{speaker} */
    public function showSpeaker(Request $request, Speaker $speaker)
    {
        abort_unless($speaker->type == 1, 404);
        return new SpeakerResource($speaker);
    }

    /**
     * GET /api/client/speakers/{speaker}/times
     * params: date_from, date_to, time_from(HH:MM), time_to(HH:MM), include_unavailable(0|1), active(0|1)
     * افتراضيًا نعرض المتاح فقط (status=available & active=1)
     */
    public function speakerTimes(Request $request, Speaker $speaker)
    {
        abort_unless($speaker->type == 1, 404);

        $includeUnavailable = $request->boolean('include_unavailable', false);
        $activeFilter = $request->query('active'); // null|0|1

        $q = SpeakerTime::query()->where('speaker_id', $speaker->id);

        if (!$includeUnavailable) {
            $q->where('status','available')->where('active',1);
        }

        if ($request->filled('date_from')) {
            $q->whereDate('date', '>=', $request->query('date_from'));
        }
        if ($request->filled('date_to')) {
            $q->whereDate('date', '<=', $request->query('date_to'));
        }
        if ($request->filled('time_from')) {
            $q->where('time_to', '>', $request->query('time_from'));
        }
        if ($request->filled('time_to')) {
            $q->where('time_from', '<', $request->query('time_to'));
        }
        if (!is_null($activeFilter)) {
            $q->where('active', $activeFilter ? 1 : 0);
        }

        $times = $q->orderBy('date')->orderBy('time_from')->paginate(20);
        return SpeakerTimeResource::collection($times);
    }

    /** GET /api/client/bookings?status=&date_from=&date_to= */
    public function myBookings(Request $request)
    {
        $client = $request->user('client');

        $q = Booking::with(['speaker','client','speakerTime'])
            ->where('client_id', $client->id);

        if ($st = $request->query('status')) {
            $q->where('status', $st);
        }
        if ($df = $request->query('date_from')) {
            $q->whereDate('time_from', '>=', $df);
        }
        if ($dt = $request->query('date_to')) {
            $q->whereDate('time_from', '<=', $dt);
        }

        $bookings = $q->orderByDesc('time_from')->paginate(20);
        return BookingResource::collection($bookings);
    }

    /** GET /api/client/bookings/{booking} */
    public function showBooking(Request $request, Booking $booking)
    {
        $client = $request->user('client');
        abort_unless($booking->client_id === $client->id, 403);

        $booking->load(['speaker','client','speakerTime']);
        return new BookingResource($booking);
    }

    /**
     * POST /api/client/bookings
     * body: speaker_id, client_id(ignored), time_from(ISO), time_to(ISO), speaker_time_id(optional), status(optional: default pending)
     */
public function createBooking(Request $request)
{
    $client = $request->user('client');

    $data = $request->validate([
        'speaker_time_id' => ['required','exists:speaker_times,id'],
    ]);

    return DB::transaction(function () use ($data, $client) {

        // اقفل الصف لمنع السباقات
        $slot = \App\Models\SpeakerTime::with('speaker')
            ->lockForUpdate()
            ->findOrFail($data['speaker_time_id']);

        // تحقق من صلاحية السبيكر والفتحة
        if (($slot->speaker?->type ?? null) !== 1) {
            return response()->json(['message' => 'Invalid speaker'], 422);
        }
        if (!$slot->active || $slot->status !== 'available') {
            return response()->json(['message' => 'Slot not available'], 422);
        }

        // جهّز التاريخ كسلسلة "Y-m-d"
        $slotDate = $slot->date instanceof Carbon
            ? $slot->date->toDateString()
            : substr((string)$slot->date, 0, 10);

        // هات القيم الخام من الداتا بيز لو موجودة، أو استخدم القيمة الحالية
        $rawFrom = $slot->getRawOriginal('time_from') ?? (string)$slot->time_from;
        $rawTo   = $slot->getRawOriginal('time_to')   ?? (string)$slot->time_to;

        // لو الوقت فيه تاريخ ملزوق قدامه، شيله (sanitize)
        $rawFrom = preg_replace('/^\d{4}-\d{2}-\d{2}\s+/', '', trim($rawFrom));
        $rawTo   = preg_replace('/^\d{4}-\d{2}-\d{2}\s+/', '', trim($rawTo));

        // حدّد فورمات الوقت (H:i أو H:i:s)
        $fromFormat = strlen($rawFrom) === 5 ? 'Y-m-d H:i' : 'Y-m-d H:i:s';
        $toFormat   = strlen($rawTo)   === 5 ? 'Y-m-d H:i' : 'Y-m-d H:i:s';

        // ابنِ الـ DateTime من التاريخ + الوقت النظيف
        try {
            $from = Carbon::createFromFormat($fromFormat, "$slotDate $rawFrom");
            $to   = Carbon::createFromFormat($toFormat,   "$slotDate $rawTo");
        } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            // fallback أخير لو الوقت طالع بشكل غريب
            $from = Carbon::parse("$slotDate $rawFrom");
            $to   = Carbon::parse("$slotDate $rawTo");
        }

        // امنع حجز نفس الفتحة أكثر من مرة (pending/confirmed)
        $slotAlreadyBooked = \App\Models\Booking::where('speaker_time_id', $slot->id)
            ->whereIn('status', ['pending','confirmed'])
            ->exists();
        if ($slotAlreadyBooked) {
            return response()->json(['message' => 'Slot already booked'], 422);
        }

        // امنع التعارض مع أي حجز آخر لنفس السبيكر
        $overlap = \App\Models\Booking::where('speaker_id', $slot->speaker_id)
            ->where('time_from','<',$to)
            ->where('time_to','>',$from)
            ->whereIn('status', ['pending','confirmed'])
            ->exists();
        if ($overlap) {
            return response()->json(['message' => 'Overlaps with another booking for this speaker'], 422);
        }

        // إنشاء الحجز بما يغطي الفتحة كاملة
        $booking = \App\Models\Booking::create([
            'speaker_id'      => $slot->speaker_id,
            'client_id'       => $client->id,
            'speaker_time_id' => $slot->id,
            'time_from'       => $from,
            'time_to'         => $to,
            'status'          => 'pending',
            'active'          => true,
        ]);

        // علّم الفتحة كمحجوزة
        $slot->update(['status' => 'booked']);

        $booking->load(['speaker','client','speakerTime']);
        return (new \App\Http\Resources\Booking\BookingResource($booking))
            ->response()->setStatusCode(201);
    });
}


}
