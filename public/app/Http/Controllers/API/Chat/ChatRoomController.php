<?php

namespace App\Http\Controllers\API\Chat;

use App\Http\Controllers\Controller;
use App\Http\Resources\Chat\ChatParticipantResource;
use App\Http\Resources\Chat\ChatRoomCollection;
use App\Http\Resources\Chat\ChatRoomResource;
use App\Models\Chat\ChatParticipant;
use App\Models\Chat\ChatRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;          // <-- مفقودة قبل كده
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ChatRoomController extends Controller
{
    public function index(Request $request)
    {
        $perPage       = (int) $request->integer('per_page', 25);
        $withMessages  = $request->boolean('with_messages', false);
        $messagesLimit = max(0, (int) $request->integer('messages_limit', 0)); // 0 = no embed
        $messagesLimit = $withMessages && $messagesLimit === 0 ? 1 : $messagesLimit; // default 1 if with_messages

        $bookingRegex = '^booking-[0-9]+-client-[0-9]+-sponsor-[0-9]+$';

        // Subquery: آخر وقت رسالة لكل روم
        $lastMsgSub = DB::table('chat_messages')
            ->selectRaw('room_id, MAX(created_at) AS last_message_at')
            ->groupBy('room_id');

        $q = ChatRoom::query()
            ->select('chat_rooms.*', 'lm.last_message_at') // lm = last message
            ->leftJoinSub($lastMsgSub, 'lm', 'lm.room_id', '=', 'chat_rooms.id')

            // فلاتر البحث
            ->when($s = $request->query('q'), fn ($qq) =>
                $qq->where('chat_rooms.title', 'like', "%{$s}%"))
            ->when($request->filled('schedule_id'), fn ($qq) =>
                $qq->where('chat_rooms.schedule_id', (int) $request->query('schedule_id')))
            ->when($kind = strtolower((string) $request->query('room_kind', '')), function ($qq) use ($bookingRegex, $kind) {
                if ($kind === 'booking') {
                    $qq->where('chat_rooms.slug', 'REGEXP', $bookingRegex);
                } elseif ($kind === 'schedule') {
                    $qq->whereNotNull('chat_rooms.schedule_id')
                       ->where(function ($w) use ($bookingRegex) {
                           $w->whereNull('chat_rooms.slug')->orWhere('chat_rooms.slug', 'NOT REGEXP', $bookingRegex);
                       });
                } elseif ($kind === 'generic') {
                    $qq->whereNull('chat_rooms.schedule_id')
                       ->where(function ($w) use ($bookingRegex) {
                           $w->whereNull('chat_rooms.slug')->orWhere('chat_rooms.slug', 'NOT REGEXP', $bookingRegex);
                       });
                }
            })
            ->when($request->filled('is_booking'), function ($qq) use ($request, $bookingRegex) {
                $is = $request->boolean('is_booking');
                $qq->where('chat_rooms.slug', $is ? 'REGEXP' : 'NOT REGEXP', $bookingRegex);
            })

            // counts
            ->withCount(['participants', 'messages']);

        // تضمين آخر رسائل (اختياري)
        if ($messagesLimit > 0) {
            $q->with(['messages' => function ($mq) use ($messagesLimit) {
                $mq->latest('id')->with('sender')->limit($messagesLimit);
            }]);
        }

        // الترتيب: آخر رسالة أولاً، ثم updated_at ثم created_at كـ fallback
        $q->orderByRaw('COALESCE(lm.last_message_at, chat_rooms.updated_at, chat_rooms.created_at) DESC, chat_rooms.id DESC');

        $paginator = $q->paginate($perPage)->appends($request->query());

        $this->enrichRooms($paginator->getCollection());

        return new ChatRoomCollection($paginator);
    }

    public function store(Request $request)
    {
        [$actorShort, $actorId] = $this->currentActorShort();

        $data = $request->validate([
            'schedule_id' => ['required', 'integer', 'exists:event_schedules,id'],
            'title'       => ['nullable', 'string', 'max:255'],
            'slug'        => ['nullable', 'string', 'max:255', Rule::unique('chat_rooms', 'slug')],
        ]);

        $slug = $data['slug'] ?? $this->uniqueSlug($data['title'] ?? null, (int) $data['schedule_id']);

        $room = ChatRoom::create([
            'schedule_id'     => (int) $data['schedule_id'],
            'slug'            => $slug,
            'title'           => $data['title'] ?? null,
            'created_by_type' => $actorShort,   // client|speaker|sponsor
            'created_by_id'   => $actorId,
            'is_active'       => true,
        ]);

        ChatParticipant::firstOrCreate([
            'room_id'          => $room->id,
            'participant_type' => $actorShort, // short
            'participant_id'   => $actorId,
        ]);

        $this->enrichRooms(collect([$room]));

        // بث إنشاء الغرفة
        $this->triggerRoom($room->id, 'room.created', [
            'room' => (new ChatRoomResource($room->loadCount(['participants','messages'])))->resolve()
        ]);

        return (new ChatRoomResource(
            $room->loadCount(['participants', 'messages'])
        ));
    }

    public function show(Request $request, string $idOrSlug)
    {
        $withMessages  = $request->boolean('with_messages', false);
        $messagesLimit = max(0, (int) $request->integer('messages_limit', 20));

        $room = $this->findRoom($idOrSlug)->loadCount(['participants', 'messages']);

        if ($withMessages && $messagesLimit > 0) {
            $room->load(['messages' => function ($mq) use ($messagesLimit) {
                $mq->latest('id')->with('sender')->limit($messagesLimit);
            }]);
        }

        $this->enrichRooms(collect([$room]));

        return new ChatRoomResource($room);
    }

    public function update(Request $request, string $idOrSlug)
    {
        $room = $this->findRoom($idOrSlug);

        $data = $request->validate([
            'title'     => ['nullable','string','max:255'],
            'is_active' => ['nullable','boolean'],
        ]);

        $room->fill($data)->save();
        $this->enrichRooms(collect([$room]));

        // بث تحديث الغرفة
        $this->triggerRoom($room->id, 'room.updated', [
            'room' => (new ChatRoomResource($room->loadCount(['participants','messages'])))->resolve()
        ]);

        return new ChatRoomResource(
            $room->loadCount(['participants', 'messages'])
        );
    }

    public function destroy(string $idOrSlug)
    {
        $room = $this->findRoom($idOrSlug);
        $roomId = $room->id;

        $room->delete();

        // بث حذف الغرفة
        $this->triggerRoom($roomId, 'room.deleted', ['room_id' => $roomId]);

        return response()->json(['message' => 'Room deleted']);
    }

    /**
     * POST /api/chat/rooms/{idOrSlug?}/join
     * body: { schedule_id?, title?, slug? }
     *
     * ممنوع انضمام الـ Client لو حالته pending / awaiting_verification (أو التهجئة الغلط awaitng_verification).
     */
    public function join(Request $request, ?string $idOrSlug = null)
    {
        [$actorShort, $actorId] = $this->currentActorShort();

        // ===== Gate: block pending/unverified clients =====
        if ($actorShort === 'client') {
            $client = Auth::guard('client')->user(); // نفس المستخدم الحالي
            // لو لأي سبب الجارد مختلف، نحاول نجيبه يدويًا:
            if (!$client && class_exists(\App\Models\Client::class)) {
                $client = \App\Models\Client::find($actorId);
            }

            $raw = strtolower(trim((string) ($client->status ?? $client->state ?? '')));
            // تطبيع التهجئات الشائعة
            $normalized = match ($raw) {
                'awaitng_verification' => 'awaiting_verification',
                default                 => $raw,
            };

            $blocked = in_array($normalized, ['pending', 'awaiting_verification'], true);

            if ($blocked) {
                return response()->json([
                    'message' => __('Your account is not verified yet. Please complete verification before joining the chat.')
                ], 403);
            }
        }
        // ===== end Gate =====

        $room = null;
        if (!empty($idOrSlug)) {
            $room = $this->findRoomOrNull($idOrSlug);
        }

        if (!$room) {
            $payload = $request->validate([
                'schedule_id' => ['required','integer','exists:event_schedules,id'],
                'title'       => ['nullable','string','max:255'],
                'slug'        => ['nullable','string','max:255', Rule::unique('chat_rooms','slug')],
            ]);

            $room = $this->findRoomByScheduleId((int) $payload['schedule_id']);

            if (!$room) {
                $slug = $payload['slug'] ?? $this->uniqueSlug($payload['title'] ?? null, (int) $payload['schedule_id']);

                $room = ChatRoom::create([
                    'schedule_id'     => (int) $payload['schedule_id'],
                    'slug'            => $slug,
                    'title'           => $payload['title'] ?? null,
                    'created_by_type' => $actorShort,
                    'created_by_id'   => $actorId,
                    'is_active'       => true,
                ]);

                // بث إنشاء الغرفة (حالة الإنشاء من join)
                $this->triggerRoom($room->id, 'room.created', [
                    'room' => (new ChatRoomResource($room->loadCount(['participants','messages'])))->resolve()
                ]);
            }
        }

        $p = ChatParticipant::firstOrCreate([
            'room_id'          => $room->id,
            'participant_type' => $actorShort,
            'participant_id'   => $actorId,
        ]);

        $this->enrichRooms(collect([$room]));

        // بث انضمام مشارك
        $this->triggerRoom($room->id, 'participant.joined', [
            'room_id'     => $room->id,
            'participant' => (new ChatParticipantResource($p))->resolve(),
        ]);

        // (اختياري) بث فردي للمشارك المنضم
        $this->triggerUser($actorShort, $actorId, 'participant.joined', [
            'room_id'     => $room->id,
            'participant' => (new ChatParticipantResource($p))->resolve(),
        ]);

        return new ChatParticipantResource($p->loadMissing('room'));
    }

    public function leave(Request $request, string $idOrSlug)
    {
        [$actorShort, $actorId] = $this->currentActorShort();

        $room = $this->findRoom($idOrSlug);

        $deleted = ChatParticipant::where([
            'room_id'          => $room->id,
            'participant_type' => $actorShort,
            'participant_id'   => $actorId,
        ])->delete();

        if ($deleted) {
            // بث مغادرة مشارك
            $this->triggerRoom($room->id, 'participant.left', [
                'room_id' => $room->id,
                'participant' => [
                    'participant_type' => $actorShort,
                    'participant_id'   => $actorId,
                ],
            ]);

            // (اختياري) بث فردي للمستخدم المغادر
            $this->triggerUser($actorShort, $actorId, 'participant.left', [
                'room_id' => $room->id,
            ]);
        }

        return response()->json(['message' => 'Left room']);
    }

    public function participants(Request $request, string $idOrSlug)
    {
        $room = $this->findRoom($idOrSlug);
        $perPage = (int) $request->integer('per_page', 50);

        $participants = $room->participants()->latest('id')->paginate($perPage);

        return ChatParticipantResource::collection($participants);
    }

    /* ========================= Helpers ========================= */

    private function findRoom(string $idOrSlug): ChatRoom
    {
        return ChatRoom::query()
            ->when(is_numeric($idOrSlug),
                fn($q) => $q->where('id', (int) $idOrSlug),
                fn($q) => $q->where('slug', $idOrSlug)
            )->firstOrFail();
    }

    private function findRoomOrNull(string $idOrSlug): ?ChatRoom
    {
        return ChatRoom::query()
            ->when(is_numeric($idOrSlug),
                fn($q) => $q->where('id', (int) $idOrSlug),
                fn($q) => $q->where('slug', $idOrSlug)
            )->first();
    }

    private function findRoomByScheduleId(int $scheduleId): ?ChatRoom
    {
        return ChatRoom::query()->where('schedule_id', $scheduleId)->first();
    }

    private function uniqueSlug(?string $title, int $scheduleId): string
    {
        $base = $title ? Str::slug($title) : 'room-'.$scheduleId;
        $slug = $base; $i = 1;
        while (ChatRoom::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }

    private function currentActorShort(): array
    {
        foreach (['client', 'sanctum', 'speaker', 'sponsor'] as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                $short = $this->inferShortFromUser($user, $guard); // client|speaker|sponsor
                $id    = $this->safeUserId($user);

                return [$short, (int) $id];
            }
        }

        abort(401, 'Unauthenticated');
    }

    private function inferShortFromUser(object $user, string $guard): string
    {
        $allowed = ['client','speaker','sponsor'];

        $base = strtolower(class_basename($user));
        if (in_array($base, $allowed, true)) return $base;

        $candidate = strtolower((string) ($user->type ?? $user->role ?? $guard));
        if (in_array($candidate, $allowed, true)) return $candidate;

        if (($user->role ?? null) === 'sponsor' || ($user->type ?? null) === 'sponsor') return 'sponsor';

        return $guard === 'speaker' ? 'speaker' : 'client';
    }

    private function safeUserId(object $user): int
    {
        if (method_exists($user, 'getAuthIdentifier')) return (int) $user->getAuthIdentifier();
        if (method_exists($user, 'getKey'))            return (int) $user->getKey();
        if (property_exists($user, 'id'))              return (int) $user->id;
        abort(401, 'Cannot resolve user id');
    }

    /**
     * enrichRooms: display_name / display_avatar / booking_meta / room_kind
     */
    private function enrichRooms($rooms): void
    {
        $scheduleIds = collect($rooms)->pluck('schedule_id')->filter()->unique()->values()->all();

        $schedulesMap = [];
        if (!empty($scheduleIds) && class_exists(\App\Models\Schedule::class)) {
            $scheds = \App\Models\Schedule::query()
                ->whereIn('id', $scheduleIds)
                ->get(['id', 'title_en', 'logo']);

            $schedulesMap = $scheds->keyBy('id')->map(fn($s) => [
                'title_en' => $s->title_en ?? null,
                'logo'     => $s->logo ?? null,
            ])->all();
        }

        foreach ($rooms as $room) {
            $displayName   = $room->title ?? 'Chat';
            $displayAvatar = null;

            if (!empty($room->schedule_id) && isset($schedulesMap[$room->schedule_id])) {
                $displayName   = (string) ($schedulesMap[$room->schedule_id]['title_en'] ?? $displayName);
                $displayAvatar = $schedulesMap[$room->schedule_id]['logo'] ?? null;
            }

            $slug = is_string($room->slug) ? $room->slug : '';
            $isDirectBookingRoom = false;
            $bookingMeta = null;

            if ($slug !== '' && preg_match('/^booking-(\d+)-client-(\d+)-sponsor-(\d+)$/i', $slug, $m)) {
                $isDirectBookingRoom = true;
                $bookingMeta = [
                    'booking_id' => (int) $m[1],
                    'client_id'  => (int) $m[2],
                    'sponsor_id' => (int) $m[3],
                ];
            }

            $roomKind = $isDirectBookingRoom
                ? 'booking'
                : (!empty($room->schedule_id) ? 'schedule' : 'generic');

            $room->setAttribute('display_name', $displayName);
            $room->setAttribute('display_avatar', $displayAvatar);
            $room->setAttribute('is_direct_booking_room', $isDirectBookingRoom);
            $room->setAttribute('booking_meta', $bookingMeta);
            $room->setAttribute('room_kind', $roomKind);
        }
    }

    /* ========================= Pusher RAW helpers ========================= */

    private function roomChannel(int $roomId): string
    {
        return "private-chat.room.{$roomId}";
    }

    private function userChannel(string $type, int $id): string
    {
        return "private-chat.user.{$type}.{$id}";
    }

    private function triggerRoom(int $roomId, string $event, array $payload): void
    {
        $this->pusherTrigger($this->roomChannel($roomId), $event, $payload);
    }

    private function triggerUser(string $type, int $id, string $event, array $payload): void
    {
        $this->pusherTrigger($this->userChannel($type, $id), $event, $payload);
    }

    /**
     * pusherTrigger: يضرب REST API مباشرة (بدون SDK)
     * - يوّقع بالـ HMAC SHA256 طبقًا لتوثيق Pusher
     */
    private function pusherTrigger(string $channel, string $event, array $data, ?string $socketId = null): void
    {
        $appId    = env('PUSHER_APP_ID');
        $key      = env('PUSHER_APP_KEY');
        $secret   = env('PUSHER_APP_SECRET');
        $cluster  = env('PUSHER_APP_CLUSTER', 'eu');

        if (!$appId || !$key || !$secret) return;

        $host   = "api-{$cluster}.pusher.com";
        $path   = "/apps/{$appId}/events";
        $scheme = "https";
        $method = "POST";

        $bodyArr = [
            'name'     => $event,
            'channels' => [$channel],
            'data'     => json_encode($data, JSON_UNESCAPED_UNICODE),
        ];
        if ($socketId) $bodyArr['socket_id'] = $socketId;

        $body    = json_encode($bodyArr, JSON_UNESCAPED_UNICODE);
        $bodyMd5 = md5($body);

        $query = [
            'auth_key'       => $key,
            'auth_timestamp' => (string) time(),
            'auth_version'   => '1.0',
            'body_md5'       => $bodyMd5,
        ];

        ksort($query);
        $queryString = http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        $stringToSign = "{$method}\n{$path}\n{$queryString}";
        $authSignature = hash_hmac('sha256', $stringToSign, $secret);
        $url = "{$scheme}://{$host}{$path}?{$queryString}&auth_signature={$authSignature}";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
        ]);
        curl_exec($ch);
        curl_close($ch);
    }
}
