<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Http\Resources\Booking\BookingResource;
use App\Models\Booking;
use App\Models\Chat\ChatParticipant;
use App\Models\Chat\ChatRoom;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function __construct(private \App\Services\SponsorScheduleService $service) {}

    /**
     * GET /api/sponsor/bookings
     */
    public function indexForSponsor(Request $request)
    {
        $sponsor = $request->user('sponsor');
        if (!$sponsor) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $status    = $request->string('status')->trim()->lower()->toString();
        $date      = $request->string('date')->trim()->toString();
        $dateFrom  = $request->string('date_from')->trim()->toString();
        $dateTo    = $request->string('date_to')->trim()->toString();
        $perPage   = (int) ($request->input('per_page', 4)) ?: 4;

        $q = Booking::query()
            ->where('sponsor_id', $sponsor->id)
            ->with(['client', 'sponsor'])
            ->latest('time_from');

        if (in_array($status, ['pending','confirmed','cancelled'], true)) {
            $q->where('status', $status);
        }

        if ($date) {
            try {
                $d = Carbon::parse($date)->toDateString();
                $q->whereDate('time_from', $d);
            } catch (\Throwable $e) {}
        }

        if ($dateFrom || $dateTo) {
            try {
                if ($dateFrom) {
                    $from = Carbon::parse($dateFrom)->startOfDay();
                    $q->where('time_from', '>=', $from);
                }
                if ($dateTo) {
                    $to = Carbon::parse($dateTo)->endOfDay();
                    $q->where('time_from', '<=', $to);
                }
            } catch (\Throwable $e) {}
        }

        $paginator = $q->paginate($perPage);

        return BookingResource::collection($paginator)->additional([
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
                'has_more'     => $paginator->hasMorePages(),
            ],
            'filters' => [
                'status'    => $status ?: null,
                'date'      => $date ?: null,
                'date_from' => $dateFrom ?: null,
                'date_to'   => $dateTo ?: null,
            ],
        ]);
    }

    /**
     * POST /api/client/bookings
     * ينشئ حجز PENDING ويبلغ السبانسر بالإشعار (بدون إنشاء شات).
     */
    public function store(StoreBookingRequest $request)
    {
        $client = $request->user('client');
        if (!$client) return response()->json(['message' => 'Unauthenticated'], 401);

        $sponsorId = (int) $request->input('sponsor_id');
        $from      = Carbon::parse($request->input('time_from'));
        $to        = Carbon::parse($request->input('time_to'));

        if ($from->diffInMinutes($to) !== \App\Services\SponsorScheduleService::SLOT_MINUTES) {
            throw ValidationException::withMessages([
                'time_to' => ['Slot must be exactly '.\App\Services\SponsorScheduleService::SLOT_MINUTES.' minutes.']
            ]);
        }

        if (!in_array($from->toDateString(), \App\Services\SponsorScheduleService::DAYS, true)) {
            throw ValidationException::withMessages([
                'time_from' => ['Date is outside allowed schedule days.']
            ]);
        }

        $alreadyHasSameDayWithSponsor = Booking::query()
            ->where('client_id',  $client->id)
            ->where('sponsor_id', $sponsorId)
            ->whereIn('status', ['pending','confirmed'])
            ->exists();

        if ($alreadyHasSameDayWithSponsor) {
            return response()->json([
                'message' => 'You already have a booking with this sponsor .'
            ], 422);
        }

        $pendingCount = Booking::query()
            ->where('sponsor_id', $sponsorId)
            ->whereDate('time_from', $from->toDateString())
            ->whereTime('time_from', '>=', $from->format('H:i:s'))
            ->whereTime('time_from', '<',  $to->format('H:i:s'))
            ->where('status', 'pending')
            ->count();

        if ($pendingCount >= \App\Services\SponsorScheduleService::SLOT_CAPACITY) {
            return response()->json(['message' => 'Slot is full (pending limit reached).'], 422);
        }

        $booking = new Booking();
        $booking->client_id  = $client->id;
        $booking->sponsor_id = $sponsorId;
        $booking->speaker_id = $request->input('speaker_id'); // optional
        $booking->time_from  = $from;
        $booking->time_to    = $to;
        $booking->status     = 'pending';
        $booking->active     = true;
        $booking->save();

        $booking->load(['sponsor','client']);

        // إشعار للسبونسر (عكسي) بـ FCM v1
        $this->notifyBookingEvent(
            recipient: $booking->sponsor,
            actor:     $booking->client,
            booking:   $booking,
            verb:      'created',
            title:     "New Booking #{$booking->id}",
            body:      'A new booking request was created.'
        );

        return (new BookingResource($booking))
            ->additional(['message' => 'Booking created as pending.']);
    }

    /**
     * GET /api/bookings/{id}
     */
    public function show($id)
    {
        $booking = Booking::with(['sponsor','client'])->find($id);
        if (!$booking) return response()->json(['message' => 'Not found'], 404);

        return new BookingResource($booking);
    }

    /**
     * POST /api/sponsor/bookings/{id}/accept
     * - يقبل الحجز
     * - يضمن وجود شات **واحد فقط** بين نفس (client,sponsor) عبر كل الحجوزات
     * - يبعت إشعار للعميل
     */
    public function accept(Request $request, $id)
    {
        $sponsor = $request->user('sponsor');
        if (!$sponsor) return response()->json(['message' => 'Unauthenticated'], 401);

        $result = DB::transaction(function () use ($sponsor, $id) {
            /** @var Booking|null $booking */
            $booking = Booking::query()
                ->where('sponsor_id', $sponsor->id)
                ->where('id', $id)
                ->lockForUpdate()
                ->first();

            if (!$booking) {
                return ['error' => ['code' => 404, 'message' => 'Not found']];
            }

            if ($booking->status === 'confirmed') {
                return ['error' => ['code' => 422, 'message' => 'Booking already confirmed.']];
            }
            if ($booking->status === 'cancelled') {
                return ['error' => ['code' => 422, 'message' => 'Cannot accept a cancelled booking.']];
            }
            if ($booking->status !== 'pending') {
                return ['error' => ['code' => 422, 'message' => 'Only pending bookings can be accepted.']];
            }

            // نفّذ القبول
            $booking->status = 'confirmed';
            $booking->save();

            // ===== استخدم/أنشئ شات واحد فقط بين نفس العميل والسبونسر =====
            $room = $this->ensureSingleRoomForClientSponsor(
                bookingId:   $booking->id,
                clientId:    (int) $booking->client_id,
                sponsorId:   (int) $booking->sponsor_id,
                creatorType: 'sponsor',
                creatorId:   (int) $sponsor->id
            );

            $booking->load(['sponsor','client']);

            // إشعار للعميل (عكسي)
            $this->notifyBookingEvent(
                recipient: $booking->client,
                actor:     $booking->sponsor,
                booking:   $booking,
                verb:      'accepted',
                title:     "Booking #{$booking->id} confirmed",
                body:      'Your booking has been accepted.'
            );

            return ['booking' => $booking, 'room' => $room];
        });

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']['message']], $result['error']['code']);
        }

        /** @var Booking $booking */
        $booking = $result['booking'];
        /** @var ChatRoom $room */
        $room    = $result['room'];

        return (new BookingResource($booking))
            ->additional([
                'message'    => 'Booking accepted.',
                'chat_room'  => [
                    'id'    => (int) $room->id,
                    'slug'  => $room->slug,
                    'title' => $room->title,
                ],
            ]);
    }

    /**
     * POST /api/sponsor/bookings/{id}/reject
     * - لا ينشئ شات نهائيًا
     * - يرسل إشعار للعميل فقط
     */
    public function reject(Request $request, $id)
    {
        $sponsor = $request->user('sponsor');
        if (!$sponsor) return response()->json(['message' => 'Unauthenticated'], 401);

        $result = DB::transaction(function () use ($sponsor, $id) {
            /** @var Booking|null $booking */
            $booking = Booking::query()
                ->where('sponsor_id', $sponsor->id)
                ->where('id', $id)
                ->lockForUpdate()
                ->first();

            if (!$booking) {
                return ['error' => ['code' => 404, 'message' => 'Not found']];
            }

            if ($booking->status === 'cancelled') {
                return ['error' => ['code' => 422, 'message' => 'Booking already cancelled.']];
            }
            if ($booking->status === 'confirmed') {
                return ['error' => ['code' => 422, 'message' => 'Cannot reject a confirmed booking.']];
            }
            if ($booking->status !== 'pending') {
                return ['error' => ['code' => 422, 'message' => 'Only pending bookings can be rejected.']];
            }

            // نفّذ الرفض
            $booking->status = 'cancelled';
            $booking->save();

            $booking->load(['sponsor','client']);

            // إشعار للعميل فقط — لا شات
            $this->notifyBookingEvent(
                recipient: $booking->client,
                actor:     $booking->sponsor,
                booking:   $booking,
                verb:      'rejected',
                title:     "Booking #{$booking->id} cancelled",
                body:      'Your booking has been rejected.'
            );

            return ['booking' => $booking];
        });

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']['message']], $result['error']['code']);
        }

        /** @var Booking $booking */
        $booking = $result['booking'];

        return (new BookingResource($booking))
            ->additional(['message' => 'Booking rejected (cancelled).']);
    }

    /**
     * GET /api/schedules/sponsors
     */
    public function publicSchedules(Request $request)
    {
        $filters = $request->only(['date','status','time_from','time_to','sponsor_id']);
        $data = $this->service->allSponsorsSlots($filters);

        return response()->json(['data' => $data]);
    }

    /* ===================== Helpers ===================== */

    /**
     * يضمن وجود روم وحيد بين (client,sponsor) عبر كل الحجوزات.
     * - لو موجود أي روم بين نفس الطرفين (بغض النظر عن booking_id) يرجّعه.
     * - لو مش موجود: ينشئ روم بسلاج booking-{bookingId}-client-{clientId}-sponsor-{sponsorId}.
     */
    private function ensureSingleRoomForClientSponsor(
        int $bookingId,
        int $clientId,
        int $sponsorId,
        string $creatorType,
        int $creatorId
    ): ChatRoom {
        // ابحث عن أي شات سابق بين نفس الأطراف
        $regex = '^booking-[0-9]+-client-' . $clientId . '-sponsor-' . $sponsorId . '$';
        $room = ChatRoom::query()
            ->where('slug', 'REGEXP', $regex)
            ->orderBy('id')
            ->first();

        if (!$room) {
            // أنشئ روم جديد لهذا الـ booking
            $slug = $this->composeSlug($bookingId, $clientId, $sponsorId);
            $room = ChatRoom::create([
                'schedule_id'     => $bookingId, // لو عندك booking_id عمود مستقل استبدله
                'slug'            => $slug,
                'title'           => 'Booking #'.$bookingId,
                'created_by_type' => $creatorType, // غالبًا sponsor
                'created_by_id'   => $creatorId,
                'is_active'       => true,
            ]);
        }

        // ثبت المشاركين (مرة واحدة)
        ChatParticipant::firstOrCreate([
            'room_id'          => $room->id,
            'participant_type' => 'client',
            'participant_id'   => $clientId,
        ]);

        ChatParticipant::firstOrCreate([
            'room_id'          => $room->id,
            'participant_type' => 'sponsor',
            'participant_id'   => $sponsorId,
        ]);

        return $room;
    }

    /**
     * يُعيد [client_id, sponsor_id] لحجز معيّن
     */
    private function bookingParties(int $bookingId): array
    {
        /** @var \App\Models\Booking $booking */
        $booking = Booking::query()->findOrFail($bookingId);

        $clientId  = (int) ($booking->client_id ?? 0);
        $sponsorId = (int) ($booking->sponsor_id ?? 0);

        if ($clientId <= 0 || $sponsorId <= 0) {
            abort(422, 'Booking does not have valid client_id/sponsor_id.');
        }

        return [$clientId, $sponsorId];
    }

    /**
     * يبني سلاج ثابت
     */
    private function composeSlug(int $bookingId, int $clientId, int $sponsorId): string
    {
        return Str::lower("booking-{$bookingId}-client-{$clientId}-sponsor-{$sponsorId}");
    }

    /**
     * إشعار كامل: يسجّل في جدول notifications + يبعث FCM v1.
     * $recipient لازم يكون عنده عمود fcm_token
     */
    private function notifyBookingEvent(object $recipient, ?object $actor, Booking $booking, string $verb, string $title, string $body): void
    {
        $traceId = (string) Str::uuid();

        $subjectType = 'Booking';
        $subjectId   = (int) $booking->id;
        $subjectSlug = $this->composeSlug($booking->id, (int)$booking->client_id, (int)$booking->sponsor_id);

        // deeplink + web_url
        [$deeplink, $webUrl] = $this->buildDeeplinkAndWeb('booking', $subjectId, $subjectSlug);

        // 1) DB record
        DB::table('notifications')->insert([
            'notifiable_type' => get_class($recipient),
            'notifiable_id'   => (int) $recipient->getKey(),
            'actor_type'      => $actor ? get_class($actor) : null,
            'actor_id'        => $actor ? (int)$actor->getKey() : null,
            'subject_type'    => $subjectType,
            'subject_id'      => $subjectId,
            'subject_slug'    => $subjectSlug,
            'verb'            => $verb,
            'title'           => $title,
            'body'            => $body,
            'data'            => json_encode([
                'status'       => $booking->status,
                'room_kind'    => 'booking',
                'booking_meta' => [
                    'booking_id' => $booking->id,
                    'client_id'  => $booking->client_id,
                    'sponsor_id' => $booking->sponsor_id,
                ],
            ], JSON_UNESCAPED_UNICODE),
            'deeplink'        => $deeplink,
            'web_url'         => $webUrl,
            'delivered_at'    => now(),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // 2) FCM v1
        $token = trim((string)($recipient->fcm_token ?? ''));
        if ($token !== '') {
            $payload = [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'data' => [
                    'deeplink'     => $deeplink,
                    'web_url'      => $webUrl,
                    'subject_type' => $subjectType,
                    'subject_id'   => (string)$subjectId,
                    'subject_slug' => $subjectSlug,
                    'room_kind'    => 'booking',
                    'booking_meta' => json_encode([
                        'booking_id' => $booking->id,
                        'client_id'  => $booking->client_id,
                        'sponsor_id' => $booking->sponsor_id,
                    ], JSON_UNESCAPED_UNICODE),
                ],
                // Android/iOS خيارات إضافية لو محتاجها:
                // 'android' => ['priority' => 'HIGH'],
                // 'apns'    => ['headers' => ['apns-priority' => '10']],
            ];

            $this->sendFcmV1($payload, $traceId, [
                'recipient_model' => class_basename($recipient),
                'recipient_id'    => (int) $recipient->getKey(),
                'booking_id'      => (int) $booking->id,
            ]);
        }
    }

    /**
     * FCM v1 باستخدام Service Account JSON (من .env: FIREBASE_SA, FIREBASE_PROJECT_ID)
     */
    private function sendFcmV1(array $message, string $traceId, array $context = []): void
    {
        $saPath  = (string) env('FIREBASE_SA', '');
        $project = (string) env('FIREBASE_PROJECT_ID', '');

        if ($saPath === '' || $project === '') {
            Log::warning('FCM v1 skipped: missing FIREBASE_SA or FIREBASE_PROJECT_ID', ['trace_id' => $traceId] + $context);
            return;
        }

        // اقرأ ملف الـ SA
        $absPath = base_path($saPath); // يدعم storage/app/firebase-admin.json
        if (!is_file($absPath)) {
            Log::error('FCM v1 skipped: service account file not found', ['trace_id' => $traceId, 'path' => $absPath] + $context);
            return;
        }

        $json = file_get_contents($absPath);
        $creds = json_decode($json, true);
        if (!is_array($creds)) {
            Log::error('FCM v1 skipped: invalid service account json', ['trace_id' => $traceId, 'path' => $absPath] + $context);
            return;
        }

        $clientEmail = $creds['client_email'] ?? null;
        $privateKey  = $creds['private_key']  ?? null;
        if (!$clientEmail || !$privateKey) {
            Log::error('FCM v1 skipped: missing client_email/private_key', ['trace_id' => $traceId, 'path' => $absPath] + $context);
            return;
        }

        // اصنع JWT لطلب OAuth2 (assertion flow)
        $now   = time();
        $exp   = $now + 3600; // 1h
        $scope = 'https://www.googleapis.com/auth/firebase.messaging';
        $jwtHeader = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $jwtClaim  = base64_encode(json_encode([
            'iss'   => $clientEmail,
            'sub'   => $clientEmail,
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $exp,
            'scope' => $scope,
        ]));
        $signature = '';
        openssl_sign("{$jwtHeader}.{$jwtClaim}", $signature, $privateKey, 'sha256WithRSAEncryption');
        $jwt = "{$jwtHeader}.{$jwtClaim}.".base64_encode($signature);

        // تبادل الـ JWT لتوكين وصول
        $postFields = http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_POSTFIELDS     => $postFields,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
        ]);
        $resp = curl_exec($ch);
        $err  = curl_errno($ch) ? curl_error($ch) : null;
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err || $code !== 200) {
            Log::error('FCM v1 token error', ['trace_id' => $traceId, 'http_code' => $code, 'error' => $err, 'body' => $resp] + $context);
            return;
        }

        $tok = json_decode($resp, true);
        $accessToken = $tok['access_token'] ?? null;
        $ttl = (int) ($tok['expires_in'] ?? 0);

        if (!$accessToken) {
            Log::error('FCM v1 token missing access_token', ['trace_id' => $traceId] + $context);
            return;
        }

        Log::info('FCM v1: token obtained (file)', [
            'trace_id'  => $traceId,
            'ttl_s'     => $ttl,
            'sa_email'  => $clientEmail,
            'proj'      => $project,
            'len'       => strlen($accessToken),
            'path'      => $absPath,
        ] + $context);

        // أرسل الرسالة
        $url = "https://fcm.googleapis.com/v1/projects/{$project}/messages:send";
        $body = json_encode(['message' => $message], JSON_UNESCAPED_UNICODE);

        Log::info('FCM v1 REQUEST', [
            'trace_id'  => $traceId,
            'url'       => $url,
            'has_title' => isset($message['notification']['title']),
            'has_body'  => isset($message['notification']['body']),
            'token_tail'=> isset($message['token']) ? substr((string)$message['token'], -8) : null,
            'token_len' => isset($message['token']) ? strlen((string)$message['token']) : null,
        ] + $context);

        $ch2 = curl_init($url);
        curl_setopt_array($ch2, [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer '.$accessToken,
            ],
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_TIMEOUT        => 20,
        ]);
        $resp2 = curl_exec($ch2);
        $err2  = curl_errno($ch2) ? curl_error($ch2) : null;
        $headerSize = curl_getinfo($ch2, CURLINFO_HEADER_SIZE);
        $status2    = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
        $headersStr = substr((string)$resp2, 0, $headerSize);
        $bodyStr    = substr((string)$resp2, $headerSize);
        curl_close($ch2);

        Log::info('FCM v1 RESPONSE', [
            'trace_id'  => $traceId,
            'http_code' => $status2,
            'headers'   => $headersStr,
            'body'      => $bodyStr,
            'token_tail'=> isset($message['token']) ? substr((string)$message['token'], -8) : null,
        ] + $context);

        if ($err2 || $status2 < 200 || $status2 >= 300) {
            Log::error('FCM v1 DELIVERY ISSUE', [
                'trace_id'  => $traceId,
                'http_code' => $status2,
                'raw'       => $bodyStr,
                'token_tail'=> isset($message['token']) ? substr((string)$message['token'], -8) : null,
            ] + $context);
            return;
        }

        $parsed = json_decode($bodyStr, true);
        $name = $parsed['name'] ?? null;
        Log::info('FCM v1 RESULT', [
            'trace_id'  => $traceId,
            'name'      => $name,
            'token_tail'=> isset($message['token']) ? substr((string)$message['token'], -8) : null,
        ] + $context);
    }

    /**
     * deeplink + web url
     */
    private function buildDeeplinkAndWeb(string $module, int $id, ?string $slug = null): array
    {
        $slugPart = $slug ? "/{$slug}" : '';
        $deeplink = "app://{$module}{$slugPart}?id={$id}";
        $webUrl   = url("/{$module}{$slugPart}".($slug ? '' : "/{$id}"));
        return [$deeplink, $webUrl];
    }
}
