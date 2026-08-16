<?php
// app/Http/Controllers/Api/Speaker/SpeakerApiController.php

namespace App\Http\Controllers\API\Speaker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\{SpeakerTime, Booking};
use App\Http\Resources\SpeakerTime\SpeakerTimeResource;
use App\Http\Resources\Booking\BookingResource;

class SpeakerApiController extends Controller
{
    /**
     * GET /api/speaker/times
     * params: date_from, date_to, time_from, time_to, status, active
     */
    public function myTimes(Request $request)
    {
        $speaker = $request->user('speaker');

        $q = SpeakerTime::query()->where('speaker_id', $speaker->id);

        if ($st = $request->query('status')) {
            $q->where('status', $st); // available/booked/unavailable
        }
        if (!is_null($request->query('active'))) {
            $q->where('active', $request->boolean('active') ? 1 : 0);
        }
        if ($df = $request->query('date_from')) {
            $q->whereDate('date','>=',$df);
        }
        if ($dt = $request->query('date_to')) {
            $q->whereDate('date','<=',$dt);
        }
        if ($tf = $request->query('time_from')) {
            $q->where('time_to','>',$tf);
        }
        if ($tt = $request->query('time_to')) {
            $q->where('time_from','<',$tt);
        }

        $times = $q->orderBy('date')->orderBy('time_from')->paginate(20);
        return SpeakerTimeResource::collection($times);
    }

    /**
     * GET /api/speaker/bookings
     * params: status, date_from, date_to, include_pending(0|1 default 1)
     */
    public function myBookings(Request $request)
    {
        $speaker = $request->user('speaker');

        $q = Booking::with(['client','speakerTime'])
            ->where('speaker_id', $speaker->id);

        if ($st = $request->query('status')) {
            $q->where('status', $st);
        }
        if ($df = $request->query('date_from')) {
            $q->whereDate('time_from','>=',$df);
        }
        if ($dt = $request->query('date_to')) {
            $q->whereDate('time_from','<=',$dt);
        }

        $bookings = $q->orderBy('time_from')->paginate(20);
        return BookingResource::collection($bookings);
    }

    /** GET /api/speaker/bookings/next (أقرب حجز مؤكد قادم) */
    public function nextBooking(Request $request)
    {
        $speaker = $request->user('speaker');

        $next = Booking::with(['client','speakerTime'])
            ->where('speaker_id', $speaker->id)
            ->where('status', 'confirmed')
            ->where('time_from', '>=', Carbon::now())
            ->orderBy('time_from')
            ->first();

        return $next ? new BookingResource($next) : response()->json(['data'=>null]);
    }

    /** GET /api/speaker/bookings/{booking} */
    public function showBooking(Request $request, Booking $booking)
    {
        $speaker = $request->user('speaker');
        abort_unless($booking->speaker_id === $speaker->id, 403);

        $booking->load(['client','speakerTime']);
        return new BookingResource($booking);
    }

    /**
     * PATCH /api/speaker/bookings/{booking}/status
     * body: action=approve|reject  => status=confirmed|cancelled
     */
    public function updateBookingStatus(Request $request, Booking $booking)
    {
        $speaker = $request->user('speaker');
        abort_unless($booking->speaker_id === $speaker->id, 403);

        $data = $request->validate([
            'action' => ['required', Rule::in(['approve','reject'])],
        ]);

        $newStatus = $data['action'] === 'approve' ? 'confirmed' : 'cancelled';

        // قبل الموافقة: تأكد ما في تعارض بعد التحديث
        if ($newStatus === 'confirmed') {
            $overlap = Booking::where('speaker_id', $speaker->id)
                ->where('id','<>',$booking->id)
                ->where('time_from','<',$booking->time_to)
                ->where('time_to','>',$booking->time_from)
                ->where('status','confirmed')
                ->exists();

            if ($overlap) {
                return response()->json(['message'=>'Conflict with another confirmed booking'], 422);
            }
        }

        $booking->update(['status' => $newStatus]);
        $booking->load(['client','speakerTime']);
        return new BookingResource($booking);
    }
}
