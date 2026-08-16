<?php

namespace App\Http\Controllers\API\Chat;

use App\Http\Controllers\Controller;
use App\Http\Resources\Chat\ChatMessageCollection;
use App\Http\Resources\Chat\ChatMessageResource;
use App\Http\Resources\Chat\ChatRoomResource;
use App\Models\Booking;
use App\Models\Chat\ChatMessage;
use App\Models\Chat\ChatParticipant;
use App\Models\Chat\ChatRoom;
use App\Models\EventSchedule; // <<< NEW
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DirectChatController extends Controller
{
    /** REGEXP لنمط غرف الحجز المباشرة */
    private const BOOKING_REGEX = '^booking-[0-9]+-client-[0-9]+-sponsor-[0-9]+$';
    /** REGEXP لغرف الجدول الزمني مثل room-17 */
    private const SCHEDULE_ROOM_REGEX = '^room-([0-9]+)$';

    /* =========================================================
     | Public Endpoints
     * =======================================================*/

    public function pusherAuthorize(Request $request)
    {
        $data = $request->validate([
            'socket_id'    => ['required','string','max:255'],
            'channel_name' => ['required','string','max:255'],
        ]);

        $actor = $this->currentActorShortOptional();
        if (!$actor) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        [$actorType, $actorId] = $actor;

        $channel  = trim($data['channel_name']);
        $socketId = trim($data['socket_id']);

        $appId   = env('PUSHER_APP_ID');
        $key     = env('PUSHER_APP_KEY');
        $secret  = env('PUSHER_APP_SECRET');

        if (!$appId || !$key || !$secret) {
            Log::warning('Pusher auth skipped: missing credentials', ['channel' => $channel]);
            return response()->json(['message' => 'Pusher credentials not configured.'], 500);
        }

        $isPresence = str_starts_with($channel, 'presence-');
        $isPrivate  = str_starts_with($channel, 'private-');

        if (!($isPresence || $isPrivate)) {
            return response()->json(['message' => 'Only private/presence channels are supported.'], 422);
        }

        if (!$this->isActorAllowedForChannel($actorType, $actorId, $channel)) {
            return response()->json(['message' => 'Forbidden for this channel.'], 403);
        }

        $authPayload = [];
        if ($isPresence) {
            $userId   = "{$actorType}:{$actorId}";
            $userInfo = [
                'type' => $actorType,
                'id'   => (int) $actorId,
                'name' => $this->actorDisplayForPresence($actorType, $actorId) ?? $actorType,
            ];
            $channelData = json_encode(['user_id' => $userId, 'user_info' => $userInfo], JSON_UNESCAPED_UNICODE);
            $stringToSign = "{$socketId}:{$channel}:{$channelData}";
            $signature    = hash_hmac('sha256', $stringToSign, $secret);
            $authPayload  = [
                'auth'         => "{$key}:{$signature}",
                'channel_data' => $channelData,
            ];
        } else {
            $stringToSign = "{$socketId}:{$channel}";
            $signature    = hash_hmac('sha256', $stringToSign, $secret);
            $authPayload  = ['auth' => "{$key}:{$signature}"];
        }

        return response()->json($authPayload);
    }

    public function upsertRoom(Request $request)
    {
        $data = $request->validate([
            'booking_id' => ['required', 'integer', 'exists:bookings,id'],
            'title'      => ['nullable', 'string', 'max:255'],
        ]);

        $room = $this->ensureRoomForBooking((int)$data['booking_id'], $data['title'] ?? null);

        $this->enrichRooms(collect([$room]));

        return new ChatRoomResource($room->loadCount(['participants','messages']));
    }

    public function showRoom(Request $request)
    {
        $data = $request->validate([
            'booking_id' => ['required','integer','exists:bookings,id'],
        ]);

        $room = $this->ensureRoomForBooking((int)$data['booking_id']);

        $this->enrichRooms(collect([$room]));

        return new ChatRoomResource($room->loadCount(['participants','messages']));
    }

    public function listMessages(Request $request)
    {
        $perPage        = (int) $request->integer('per_page', 30);
        $withMessages   = $request->boolean('with_messages', true);
        $messagesLimit  = max(1, min(50, (int) $request->integer('messages_limit', 1)));

        $data = $request->validate([
            'room_id'    => ['nullable','integer','exists:chat_rooms,id'],
            'booking_id' => ['nullable','integer','exists:bookings,id'],
        ]);

        if (empty($data['room_id']) && empty($data['booking_id'])) {
            return response()->json(['message' => 'room_id or booking_id is required.'], 422);
        }

        $room = !empty($data['room_id'])
            ? ChatRoom::query()->where('id', (int)$data['room_id'])->firstOrFail()
            : $this->ensureRoomForBooking((int)$data['booking_id']);

        if ($viewer = $this->currentActorShortOptional()) {
            [$viewerType, $viewerId] = $viewer;
            $isParticipant = ChatParticipant::where([
                'room_id'          => $room->id,
                'participant_type' => $viewerType,
                'participant_id'   => $viewerId,
            ])->exists();

            if (!$isParticipant) {
                return response()->json(['message' => 'Forbidden: not a participant of this room.'], 403);
            }
        }

        if ($withMessages) {
            $room->load([
                'messages' => function ($q) use ($messagesLimit) {
                    $q->with('sender')->orderByDesc('id')->limit($messagesLimit);
                }
            ]);
        }

        $this->enrichRooms(collect([$room]));

        $messages = ChatMessage::where('room_id', $room->id)
            ->with('sender')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->query());

        return response()->json([
            'room'     => (new ChatRoomResource($room->loadCount(['participants','messages'])))->toArray($request),
            'messages' => (new ChatMessageCollection($messages))->toArray($request),
        ]);
    }

    public function sendMessage(Request $request)
    {
        [$senderTypeShort, $senderId] = $this->currentActorShort();

        $data = $request->validate([
            'room_id'        => ['nullable','integer','exists:chat_rooms,id'],
            'booking_id'     => ['nullable','integer','exists:bookings,id'],
            'message'        => ['nullable','string','max:5000'],
            'attachments'    => ['nullable','array'],
            'replied_to_id'  => ['nullable','integer','exists:chat_messages,id'],
            'target_user_id' => ['nullable','integer'],
            'socket_id'      => ['nullable','string','max:255'],
        ]);

        if (empty($data['room_id']) && empty($data['booking_id'])) {
            return response()->json(['message' => 'room_id or booking_id is required.'], 422);
        }
        if (empty($data['message']) && empty($data['attachments'])) {
            return response()->json(['message' => 'Message or attachments required.'], 422);
        }

        $room = !empty($data['room_id'])
            ? ChatRoom::query()->findOrFail((int)$data['room_id'])
            : $this->ensureRoomForBooking((int)$data['booking_id']);

        $isParticipant = ChatParticipant::where([
            'room_id'          => $room->id,
            'participant_type' => $senderTypeShort,
            'participant_id'   => $senderId,
        ])->exists();
        if (!$isParticipant) {
            return response()->json(['message' => 'Sender is not a participant of this room.'], 403);
        }

        if (!empty($data['replied_to_id'])) {
            $replyOf = ChatMessage::query()
                ->where('id', (int)$data['replied_to_id'])
                ->where('room_id', $room->id)
                ->exists();
            if (!$replyOf) {
                return response()->json(['message' => 'Invalid replied_to_id for this room.'], 422);
            }
        }

        $msg = ChatMessage::create([
            'room_id'       => $room->id,
            'sender_type'   => $senderTypeShort,
            'sender_id'     => $senderId,
            'message'       => $data['message'] ?? null,
            'attachments'   => $data['attachments'] ?? null,
            'replied_to_id' => $data['replied_to_id'] ?? null,
        ]);

        ChatParticipant::where([
            'room_id'          => $room->id,
            'participant_type' => $senderTypeShort,
            'participant_id'   => $senderId,
        ])->update(['last_read_message_id' => $msg->id]);

        $room->refresh()->loadCount(['participants','messages']);
        $msg->load('sender');

        $roomForSender = $this->roomWithViewer($room, $senderTypeShort, (int)$senderId);

        $payloadForSender = [
            'room'    => (new \App\Http\Resources\Chat\ChatRoomResource($roomForSender))->toArray($request),
            'message' => (new \App\Http\Resources\Chat\ChatMessageResource($msg))->toArray($request),
        ];

        $traceId = (string) \Illuminate\Support\Str::uuid();

        $roomChannel = 'private-chat.room.' . $room->id;
        $this->pusherTrigger($roomChannel, 'message.sent', $payloadForSender, $data['socket_id'] ?? null, $traceId);

        $participants = ChatParticipant::where('room_id', $room->id)
            ->get(['participant_type','participant_id']);

        foreach ($participants as $p) {
            $viewerType = $p->participant_type;
            $viewerId   = (int)$p->participant_id;

            $roomForViewer = $this->roomWithViewer($room, $viewerType, $viewerId);

            $payload = [
                'room'    => (new \App\Http\Resources\Chat\ChatRoomResource($roomForViewer))->toArray($request),
                'message' => (new \App\Http\Resources\Chat\ChatMessageResource($msg))->toArray($request),
            ];

            $userChannel = $this->perUserChannel($viewerType, $viewerId);

            $socket = ($viewerType === $senderTypeShort && $viewerId === (int)$senderId)
                ? ($data['socket_id'] ?? null)
                : null;

            $this->pusherTrigger($userChannel, 'message.sent', $payload, $socket, $traceId);
        }

        if (!empty($data['target_user_id'])) {
            $legacyChannel = 'private-chat-' . (int)$data['target_user_id'];
            $this->pusherTrigger($legacyChannel, 'my-event', $payloadForSender, null, $traceId);
        }

        $senderModel = $this->resolveParticipantModel($senderTypeShort, (int)$senderId);
        foreach ($participants as $p) {
            if ($p->participant_type === $senderTypeShort && (int)$p->participant_id === (int)$senderId) continue;
            $recipient = $this->resolveParticipantModel($p->participant_type, (int)$p->participant_id);
            if ($recipient) {
                $this->notifyChatMessage($recipient, $room, $msg, $senderModel, $traceId); // <<< uses room_name inside
            }
        }

        return response()->json([
            'room'     => (new \App\Http\Resources\Chat\ChatRoomResource($roomForSender))->toArray($request),
            'message'  => (new \App\Http\Resources\Chat\ChatMessageResource($msg))->toArray($request),
            'trace_id' => $traceId,
        ], 201);
    }

    /**
     * يحقن viewer على الموديل قبل تمريره للـ Resource
     */
    private function roomWithViewer(\App\Models\Chat\ChatRoom $room, string $viewerType, int $viewerId): \App\Models\Chat\ChatRoom
    {
        $clone = clone $room;
        $clone->setAttribute('_viewer_type', $viewerType);
        $clone->setAttribute('_viewer_id',   $viewerId);
        return $clone;
    }

    public function myRooms(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 20);
        [$viewerType, $viewerId] = $this->currentActorShort();

        $roomIds = ChatParticipant::query()
            ->where('participant_type', $viewerType)
            ->where('participant_id', $viewerId)
            ->pluck('room_id');

        if ($roomIds->isEmpty()) {
            return response()->json([
                'data' => [],
                'meta' => ['total' => 0, 'per_page' => $perPage, 'current_page' => 1]
            ]);
        }

        $q = ChatRoom::query()
            ->whereIn('id', $roomIds)
            ->when($s = $request->query('q'), fn($qq) => $qq->where('title', 'like', "%{$s}%"))
            ->when($kind = strtolower((string)$request->query('room_kind', '')), function ($qq) use ($kind) {
                if ($kind === 'booking') {
                    $qq->where('slug', 'REGEXP', self::BOOKING_REGEX);
                } elseif ($kind === 'schedule') {
                    $qq->whereNotNull('schedule_id')
                       ->where(function ($w) {
                           $w->whereNull('slug')->orWhere('slug', 'NOT REGEXP', self::BOOKING_REGEX);
                       });
                } elseif ($kind === 'generic') {
                    $qq->whereNull('schedule_id')
                       ->where(function ($w) {
                           $w->whereNull('slug')->orWhere('slug', 'NOT REGEXP', self::BOOKING_REGEX);
                       });
                }
            })
            ->when($request->filled('is_booking'), function ($qq) use ($request) {
                $is = $request->boolean('is_booking');
                $qq->where('slug', $is ? 'REGEXP' : 'NOT REGEXP', self::BOOKING_REGEX);
            })
            ->withCount(['participants','messages'])
            ->with([
                'participants'   => fn($q) => $q->select('id','room_id','participant_type','participant_id','last_read_message_id'),
                'latestMessage'  => fn($q) => $q->with('sender'),
            ])
            ->orderByDesc('updated_at');

        $rooms = $q->paginate($perPage)->appends($request->query());

        $rooms->getCollection()->transform(function ($room) use ($viewerType, $viewerId) {
            $room->setAttribute('_viewer_type', $viewerType);
            $room->setAttribute('_viewer_id',   $viewerId);
            return $room;
        });

        $this->enrichRooms($rooms->getCollection());

        return ChatRoomResource::collection($rooms)->response();
    }

    /* =========================================================
     | Internals: Booking/Rooms
     * =======================================================*/
    private function ensureRoomForBooking(int $bookingId, ?string $customTitle = null): ChatRoom
    {
        [$clientId, $sponsorId] = $this->bookingParties($bookingId);
        $slug = $this->composeSlug($bookingId, $clientId, $sponsorId);

        $room = ChatRoom::query()->where('slug', $slug)->first();
        if (!$room) {
            [$actorShort, $actorId] = $this->currentActorShortOptional() ?? ['client', $clientId];

            $room = ChatRoom::create([
                'schedule_id'     => $bookingId,
                'slug'            => $slug,
                'title'           => $customTitle ?: ('Booking #'.$bookingId),
                'created_by_type' => $actorShort,
                'created_by_id'   => $actorId,
                'is_active'       => true,
            ]);
        }

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

    private function bookingParties(int $bookingId): array
    {
        $booking = Booking::query()->findOrFail($bookingId);

        $clientId  = (int) ($booking->client_id ?? 0);
        $sponsorId = (int) ($booking->sponsor_id ?? 0);

        if ($clientId <= 0 || $sponsorId <= 0) {
            abort(422, 'Booking does not have valid client_id/sponsor_id.');
        }

        return [$clientId, $sponsorId];
    }

    private function composeSlug(int $bookingId, int $clientId, int $sponsorId): string
    {
        return Str::lower("booking-{$bookingId}-client-{$clientId}-sponsor-{$sponsorId}");
    }

    private function currentActorShortOptional(): ?array
    {
        foreach (['client', 'sanctum', 'speaker', 'sponsor'] as $guard) {
            if (Auth::guard($guard)->check()) {
                $user  = Auth::guard($guard)->user();
                $id    = $this->safeUserId($user);
                $short = $this->inferShortFromUser($user, $guard);
                return [$short, $id];
            }
        }
        return null;
    }

    private function currentActorShort(): array
    {
        $res = $this->currentActorShortOptional();
        if ($res) return $res;
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

    private function enrichRooms($rooms): void
    {
        $rooms = collect($rooms);

        foreach ($rooms as $room) {
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

            $isScheduleRoom = $this->isScheduleRoomSlug($slug);
            $roomKind = $isDirectBookingRoom
                ? 'booking'
                : ($isScheduleRoom || !empty($room->schedule_id) ? 'schedule' : 'generic');

            // Display name logic:
            $displayName = $room->title ?? 'Chat';
            if ($isDirectBookingRoom) {
                $displayName = 'Booking #'.($bookingMeta['booking_id'] ?? '');
            } elseif ($roomKind === 'schedule') {
                $displayName = $this->resolveRoomDisplayName($room) ?: ($room->title ?? 'Chat');
            }

            $room->setAttribute('display_name', $displayName);
            $room->setAttribute('display_avatar', null);
            $room->setAttribute('is_direct_booking_room', $isDirectBookingRoom);
            $room->setAttribute('booking_meta', $bookingMeta);
            $room->setAttribute('room_kind', $roomKind);
        }
    }

    /* =========================================================
     | AuthZ helpers for Pusher
     * =======================================================*/

    private function isActorAllowedForChannel(string $actorType, int $actorId, string $channel): bool
    {
        if (preg_match('~^(?:private|presence)-chat\.room\.(\d+)$~', $channel, $m)) {
            $roomId = (int) $m[1];
            return ChatParticipant::where([
                'room_id'          => $roomId,
                'participant_type' => $actorType,
                'participant_id'   => $actorId,
            ])->exists();
        }

        if (preg_match('~^(?:private|presence)-chat\.user\.(client|sponsor|speaker)\.(\d+)$~', $channel, $m)) {
            $t  = $m[1];
            $id = (int) $m[2];
            return $t === $actorType && $id === (int) $actorId;
        }

        if (preg_match('~^private-chat-(\d+)$~', $channel, $m)) {
            $id = (int) $m[1];
            return $id === (int) $actorId;
        }

        return false;
    }

    private function actorDisplayForPresence(string $type, int $id): ?string
    {
        $model = $this->resolveParticipantModel($type, $id);
        return $this->displayName($model);
    }

    /* =========================================================
     | Push & Helpers
     * =======================================================*/

    private function resolveParticipantModel(string $short, int $id): ?object
    {
        return match ($short) {
            'client'  => \App\Models\Client::query()->find($id),
            'sponsor' => \App\Models\Sponsor::query()->find($id),
            'speaker' => \App\Models\Speaker::query()->find($id),
            default   => null,
        };
    }

    /**
     * إشعار الرسالة: الآن يضيف room_name لو كانت الغرفة من نوع room-{id} (schedule)
     * ويُبقي العنوان = اسم المُرسل، والنص = نص الرسالة.
     */
     // اختصار اسم الغرفة مع تنظيف بادئات شائعة مثل "Booking #", "room-"
private function shortRoomName(?string $name, int $max = 24): string
{
    $name = trim((string) $name);
    if ($name === '') return '';

    // إزالة بادئات/أنماط متكررة
    // مثال: "Booking #123", "booking 123", "room-17", "Room 17:"
    $name = preg_replace('~^(booking\s*#?\s*)~i', '', $name);    // يحذف "Booking #"
    $name = preg_replace('~^room[\s:\-#]*~i', '', $name);        // يحذف "room-" أو "Room "
    $name = preg_replace('~\s+~', ' ', $name);                   // مسافات متتالية

    // لو الاسم أقصر من الحد، رجّعه كما هو
    if (mb_strlen($name) <= $max) return $name;

    // قصّ مع نقاط تعليق
    return rtrim(mb_substr($name, 0, max(1, $max - 1))) . '…';
}

    private function notifyChatMessage(object $recipient, ChatRoom $room, ChatMessage $message, ?object $sender = null, ?string $traceId = null): void
    {
        $subjectId   = (int) $room->id;
        $subjectSlug = is_string($room->slug) ? $room->slug : null;
        $roomName = $this->resolveRoomDisplayName($room);

        $senderName = $this->displayName($sender) ?: 'New message';
$shortRoom = $this->shortRoomName($roomName, 24);

// لو الاسم عبارة عن أرقام فقط (يدعم أرقام يونيكود كمان)
$isDigitsOnly = ($shortRoom !== '') && preg_match('/^\p{N}+$/u', $shortRoom);

// العنوان = اسم المرسل فقط لو shortRoom أرقام فقط، غير كده نعرض "اسم الغرفة • اسم المرسل"
$title = $isDigitsOnly
    ? $senderName
    : trim(($shortRoom !== '' ? ($shortRoom . ' • ') : '') . $senderName);
 $body  = (string) ($message->message ?: 'Sent an attachment');

        [$deeplink, $webUrl] = $this->buildDeeplinkAndWeb('chat', $subjectId, $subjectSlug);
        [$roomKind, $bookingMeta] = $this->extractBookingMetaFromSlug($subjectSlug);

        // اسم الغرفة (لو schedule room = room-17 ⇒ نجيب title_en من event_schedules عبر schedule_id)

        // subject_type كما هو سابقًا
        $subjectTypeString = ($roomKind === 'booking') ? 'chatdirect' : 'chatroom';

        DB::table('notifications')->insert([
            'notifiable_type' => get_class($recipient),
            'notifiable_id'   => (int) $recipient->getKey(),
            'actor_type'      => $sender ? get_class($sender) : null,
            'actor_id'        => $sender ? (int)$sender->getKey() : null,

            'subject_type'    => $subjectTypeString,
            'subject_id'      => $subjectId,
            'subject_slug'    => $subjectSlug,

            'verb'            => 'message',
            'title'           => $title,  // = اسم المُرسل
            'body'            => $body,   // = نص الرسالة
            'data'            => json_encode(array_filter([
                'room_kind'    => $roomKind,
                'booking_meta' => $bookingMeta,
                'message_id'   => (int) $message->id,
                'room_name'    => $roomName, // <<< NEW
            ], fn($v) => $v !== null), JSON_UNESCAPED_UNICODE),
            'deeplink'        => $deeplink,
            'web_url'         => $webUrl,
            'delivered_at'    => now(),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $token = trim((string)($recipient->fcm_token ?? ''));
        if ($token !== '') {
            $this->sendFcmV1(
                tokens: [$token],
                notification: [
                    // حسب طلبك: عنوان الإشعار = اسم المُرسل، جسم الإشعار = نص الرسالة
                    'title' => $title,
                    'body'  => $body,
                ],
                data: array_filter([
                    'deeplink'     => $deeplink,
                    'web_url'      => $webUrl,
                    'subject_type' => $subjectTypeString,
                    'subject_id'   => (string)$subjectId,
                    'subject_slug' => $subjectSlug,
                    'room_kind'    => $roomKind,
                    'booking_meta' => $bookingMeta ? json_encode($bookingMeta) : null,
                    'message_id'   => (string)$message->id,
                    'room_name'    => $roomName, // <<< NEW (للموبايل/الويب يظهروه كاسم الغرفة)
                ], fn($v) => $v !== null && $v !== ''),
                traceContext: [
                    'trace_id'        => $traceId,
                    'recipient_model' => class_basename($recipient),
                    'recipient_id'    => (int) $recipient->getKey(),
                    'room_id'         => (int) $room->id,
                    'message_id'      => (int) $message->id,
                ]
            );
        }
    }

    private function sendFcmV1(array $tokens, array $notification = [], array $data = [], array $traceContext = []): void
    {
        $traceId   = (string) ($traceContext['trace_id'] ?? Str::uuid());
        $projectId = (string) env('FIREBASE_PROJECT_ID', '');

        if ($projectId === '') {
            Log::warning('FCM v1 skipped: missing FIREBASE_PROJECT_ID', $traceContext + ['trace_id' => $traceId]);
            return;
        }

        $tokens = array_values(array_filter(array_unique($tokens)));
        if (empty($tokens)) {
            Log::info('FCM v1 skipped: no tokens', $traceContext + ['trace_id' => $traceId]);
            return;
        }

        $accessToken = $this->getGoogleAccessTokenV1FromFile($traceId, $traceContext);
        if (!$accessToken) {
            Log::error('FCM v1: failed to obtain access token', $traceContext + ['trace_id' => $traceId]);
            return;
        }
        if (strlen($accessToken) < 20) {
            Log::error('FCM v1: empty/short access token', $traceContext + [
                'trace_id' => $traceId,
                'len'      => strlen($accessToken),
            ]);
            return;
        }

        $url     = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
        $android = ['priority' => 'HIGH'];
        $apns    = ['headers' => ['apns-priority' => '10']];

        foreach ($tokens as $token) {
            $payload = [
                'message' => array_filter([
                    'token'        => $token,
                    'notification' => !empty($notification) ? [
                        'title' => (string)($notification['title'] ?? ''),
                        'body'  => (string)($notification['body']  ?? ''),
                    ] : null,
                    'data'         => array_filter($data, fn($v) => $v !== null && $v !== ''),
                    'android'      => $android,
                    'apns'         => $apns,
                ]),
            ];

            Log::info('FCM v1 REQUEST', $traceContext + [
                'trace_id'   => $traceId,
                'recipient_model' => $traceContext['recipient_model'] ?? null,
                'recipient_id'    => $traceContext['recipient_id']    ?? null,
                'room_id'         => $traceContext['room_id']         ?? null,
                'message_id'      => $traceContext['message_id']      ?? null,
                'url'        => $url,
                'has_title'  => isset($notification['title']),
                'has_body'   => isset($notification['body']),
                'token_tail' => substr($token, -12),
                'token_len'  => strlen($accessToken),
            ]);

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Authorization: Bearer '.$accessToken,
                ],
                CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_HEADER         => true,
            ]);
            $raw  = curl_exec($ch);
            $info = curl_getinfo($ch);
            $http = (int) ($info['http_code'] ?? 0);
            $hs   = (int) ($info['header_size'] ?? 0);
            $respHeaders = substr($raw, 0, $hs);
            $respBody    = substr($raw, $hs);
            curl_close($ch);

            Log::info('FCM v1 RESPONSE', $traceContext + [
                'trace_id'  => $traceId,
                'recipient_model' => $traceContext['recipient_model'] ?? null,
                'recipient_id'    => $traceContext['recipient_id']    ?? null,
                'room_id'         => $traceContext['room_id']         ?? null,
                'message_id'      => $traceContext['message_id']      ?? null,
                'http_code' => $http,
                'headers'   => $respHeaders,
                'body'      => $respBody,
                'token_tail'=> substr($token, -12),
            ]);

            if ($http !== 200) {
                Log::error('FCM v1 DELIVERY ISSUE', $traceContext + [
                    'trace_id'  => $traceId,
                    'recipient_model' => $traceContext['recipient_model'] ?? null,
                    'recipient_id'    => $traceContext['recipient_id']    ?? null,
                    'room_id'         => $traceContext['room_id']         ?? null,
                    'message_id'      => $traceContext['message_id']      ?? null,
                    'http_code' => $http,
                    'raw'       => $respBody,
                    'token_tail'=> substr($token, -12),
                ]);
                continue;
            }

            $json = json_decode($respBody, true);
            Log::info('FCM v1 RESULT', $traceContext + [
                'trace_id'   => $traceId,
                'name'       => $json['name'] ?? null,
                'token_tail' => substr($token, -12),
            ]);
        }
    }

    private function getGoogleAccessTokenV1FromFile(string $traceId, array $traceContext = []): ?string
    {
        $saPathEnv = (string) env('FIREBASE_SA', '');
        $projectId = (string) env('FIREBASE_PROJECT_ID', '');

        if ($saPathEnv === '' || $projectId === '') {
            Log::error('FCM v1: missing SA or PROJECT_ID', $traceContext + ['trace_id' => $traceId]);
            return null;
        }

        $resolvedPath = $this->resolveServiceAccountPath($saPathEnv);
        if (!$resolvedPath || !is_file($resolvedPath) || !is_readable($resolvedPath)) {
            Log::error('FCM v1: service account file not found/unreadable', $traceContext + [
                'trace_id' => $traceId,
                'given'    => $saPathEnv,
                'resolved' => $resolvedPath,
            ]);
            return null;
        }

        $saJson = @file_get_contents($resolvedPath);
        $sa     = json_decode((string)$saJson, true);
        if (!is_array($sa) || empty($sa['client_email']) || empty($sa['private_key'])) {
            Log::error('FCM v1: invalid service account JSON', $traceContext + [
                'trace_id' => $traceId,
                'path'     => $resolvedPath,
            ]);
            return null;
        }

        $cacheKey = 'fcm_v1_access_token:'.md5(($sa['client_email'] ?? 'na').'|'.$projectId);
        if ($token = Cache::get($cacheKey)) {
            return $token;
        }

        $now = time();
        $jwtHeader = ['alg' => 'RS256', 'typ' => 'JWT'];
        $jwtClaim  = [
            'iss'   => $sa['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ];

        $b64 = fn($d) => rtrim(strtr(base64_encode($d), '+/', '-_'), '=');
        $segments     = [$b64(json_encode($jwtHeader)), $b64(json_encode($jwtClaim))];
        $signingInput = implode('.', $segments);

        $signature = '';
        $ok = openssl_sign($signingInput, $signature, $sa['private_key'], 'sha256');
        if (!$ok) {
            Log::error('FCM v1: openssl_sign failed', $traceContext + [
                'trace_id' => $traceId,
                'path'     => $resolvedPath,
            ]);
            return null;
        }

        $jwt = $signingInput.'.'.$b64($signature);

        $post = http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_POSTFIELDS     => $post,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_HEADER         => true,
        ]);
        $raw  = curl_exec($ch);
        $info = curl_getinfo($ch);
        $http = (int) ($info['http_code'] ?? 0);
        $hs   = (int) ($info['header_size'] ?? 0);
        $respBody = substr($raw, $hs);
        curl_close($ch);

        if ($http !== 200) {
            Log::error('FCM v1: token exchange failed', $traceContext + [
                'trace_id'  => $traceId,
                'http_code' => $http,
                'raw'       => $respBody,
                'sa_email'  => $sa['client_email'] ?? null,
                'proj'      => $projectId,
            ]);
            return null;
        }

        $json        = json_decode($respBody, true);
        $accessToken = $json['access_token'] ?? null;
        $expiresIn   = (int) ($json['expires_in'] ?? 3600);

        if (!$accessToken) {
            Log::error('FCM v1: token missing in response', $traceContext + [
                'trace_id' => $traceId,
                'sa_email' => $sa['client_email'] ?? null,
                'proj'     => $projectId,
            ]);
            return null;
        }

        Cache::put($cacheKey, $accessToken, now()->addSeconds(min($expiresIn - 300, 3300)));

        Log::info('FCM v1: token obtained (file)', $traceContext + [
            'trace_id' => $traceId,
            'ttl_s'    => min($expiresIn - 300, 3300),
            'sa_email' => $sa['client_email'] ?? null,
            'proj'     => $projectId,
            'len'      => strlen($accessToken),
            'path'     => $resolvedPath,
        ]);

        return $accessToken;
    }

    private function resolveServiceAccountPath(string $saPathEnv): ?string
    {
        $saPathEnv = trim($saPathEnv);

        if ($this->isAbsolutePath($saPathEnv) && is_file($saPathEnv)) {
            return $saPathEnv;
        }

        if (preg_match('~^storage[\\/]+~i', $saPathEnv)) {
            $candidate = base_path($saPathEnv);
            if (is_file($candidate)) return $candidate;

            $alt = storage_path(preg_replace('~^storage[\\/]+~i', '', $saPathEnv));
            if (is_file($alt)) return $alt;
        }

        $baseCandidate = base_path($saPathEnv);
        if (is_file($baseCandidate)) return $baseCandidate;

        $storageCandidate = storage_path($saPathEnv);
        if (is_file($storageCandidate)) return $storageCandidate;

        $storageApp = storage_path('app/'.ltrim($saPathEnv, '/\\'));
        if (is_file($storageApp)) return $storageApp;

        return null;
    }

    private function isAbsolutePath(string $path): bool
    {
        return str_starts_with($path, '/')
            || preg_match('~^[A-Za-z]:\\\\~', $path) === 1;
    }

    private function buildDeeplinkAndWeb(string $module, int $id, ?string $slug = null): array
    {
        $slugPart = $slug ? "/{$slug}" : '';
        $deeplink = "app://{$module}{$slugPart}?id={$id}";
        $webUrl   = url("/{$module}{$slugPart}".($slug ? '' : "/{$id}"));
        return [$deeplink, $webUrl];
    }

    private function extractBookingMetaFromSlug(?string $slug): array
    {
        if (is_string($slug) && preg_match('/^booking-(\d+)-client-(\d+)-sponsor-(\d+)$/i', $slug, $m)) {
            return ['booking', [
                'booking_id' => (int) $m[1],
                'client_id'  => (int) $m[2],
                'sponsor_id' => (int) $m[3],
            ]];
        }
        return ['generic', null];
    }

    private function displayName(?object $user): ?string
    {
        if (!$user) return null;
        foreach (['name','full_name','username','title'] as $attr) {
            if (isset($user->{$attr}) && is_string($user->{$attr}) && $user->{$attr} !== '') {
                return $user->{$attr};
            }
        }
        return class_basename($user);
    }

    private function perUserChannel(string $type, int $id): string
    {
        return "private-chat.user.{$type}.{$id}";
    }

    /* =========================================================
     | Pusher RAW REST (بدون SDK) + Logging
     * =======================================================*/
    private function pusherTrigger(string $channel, string $event, array $data, ?string $socketId = null, ?string $traceId = null): void
    {
        $appId    = env('PUSHER_APP_ID');
        $key      = env('PUSHER_APP_KEY');
        $secret   = env('PUSHER_APP_SECRET');
        $cluster  = env('PUSHER_APP_CLUSTER', 'eu');

        if (!$appId || !$key || !$secret) {
            Log::warning('Pusher skipped: missing credentials', ['channel' => $channel, 'event' => $event, 'trace_id' => $traceId]);
            return;
        }

        $host   = "api-{$cluster}.pusher.com";
        $path   = "/apps/{$appId}/events";
        $scheme = "https";
        $method = "POST";
        $traceId = $traceId ?: (string) Str::uuid();

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
        $queryString  = http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        $stringToSign = "{$method}\n{$path}\n{$queryString}";
        $authSignature = hash_hmac('sha256', $stringToSign, $secret);
        $url = "{$scheme}://{$host}{$path}?{$queryString}&auth_signature={$authSignature}";

        Log::info('Pusher REQUEST', [
            'trace_id' => $traceId,
            'channel'  => $channel,
            'event'    => $event,
            'has_socket_id' => (bool) $socketId,
            'body_size' => strlen($body),
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
        ]);
        $response = curl_exec($ch);
        $errno    = curl_errno($ch);
        $error    = $errno ? curl_error($ch) : null;
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno) {
            Log::error('Pusher CURL ERROR', [
                'trace_id' => $traceId,
                'channel'  => $channel,
                'event'    => $event,
                'errno'    => $errno,
                'error'    => $error,
            ]);
            return;
        }

        Log::info('Pusher RESPONSE', [
            'trace_id' => $traceId,
            'channel'  => $channel,
            'event'    => $event,
            'http_code'=> $httpCode,
            'raw_len'  => is_string($response) ? strlen($response) : 0,
            'body'     => $response,
        ]);

        if ($httpCode < 200 || $httpCode >= 300) {
            Log::error('Pusher DELIVERY ISSUE', [
                'trace_id' => $traceId,
                'channel'  => $channel,
                'event'    => $event,
                'http_code'=> $httpCode,
                'response' => $response,
            ]);
        }
    }

    /* =========================================================
     | NEW: Helpers for Schedule Rooms & Names
     * =======================================================*/

    /** هل السلاق هو من نوع room-{id}؟ */
    private function isScheduleRoomSlug(?string $slug): bool
    {
        if (!is_string($slug) || $slug === '') return false;
        return (bool) preg_match('/'.self::SCHEDULE_ROOM_REGEX.'/i', $slug);
    }

    /**
     * اسم العرض للغرفة:
     * - لو booking ⇒ "Booking #{id}"
     * - لو slug = room-{n} أو roomKind = schedule ⇒ title_en من event_schedules عبر schedule_id
     * - غير كده ⇒ title من الغرفة أو "Chat"
     */
    private function resolveRoomDisplayName(ChatRoom $room): ?string
    {
        $slug = is_string($room->slug) ? $room->slug : null;

        // booking
        if ($slug && preg_match('/^booking-(\d+)-client-(\d+)-sponsor-(\d+)$/i', $slug, $m)) {
            return 'Booking #'.((int)$m[1]);
        }

        // schedule room by slug or schedule_id
        if ($this->isScheduleRoomSlug($slug) || !empty($room->schedule_id)) {
            $scheduleId = (int) ($room->schedule_id ?? 0);
            if ($scheduleId > 0) {
                $cacheKey = 'schedule_title_en:'.$scheduleId;
                return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($scheduleId) {
                    return (string) (EventSchedule::query()->where('id', $scheduleId)->value('title_en') ?? '');
                }) ?: null;
            }
        }

        return $room->title ?: 'Chat';
    }
}
