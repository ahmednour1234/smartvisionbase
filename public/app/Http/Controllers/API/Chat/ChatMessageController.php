<?php

namespace App\Http\Controllers\API\Chat;

use App\Http\Controllers\Controller;
use App\Http\Resources\Chat\ChatMessageCollection;
use App\Http\Resources\Chat\ChatMessageResource;
use App\Http\Resources\Chat\ChatRoomResource;
use App\Models\Chat\ChatMessage;
use App\Models\Chat\ChatParticipant;
use App\Models\Chat\ChatRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatMessageController extends Controller
{
    /**
     * GET /api/chat/rooms/{idOrSlug}/messages?per_page=30
     * يرجّع بيانات الغرفة + رسائلها بشكل مُجمّع.
     */
    public function index(Request $request, string $idOrSlug)
    {
        $perPage = (int) $request->integer('per_page', 30);

        $room = $this->findRoom($idOrSlug)
            ->loadCount(['participants', 'messages']);

        $messages = ChatMessage::where('room_id', $room->id)
            ->with('sender') // eager-load polymorphic sender
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        return response()->json([
            'room'     => (new ChatRoomResource($room))->toArray($request),
            'messages' => (new ChatMessageCollection($messages))->toArray($request),
        ]);
    }

    /**
     * POST /api/chat/rooms/{idOrSlug}/messages
     * يضيف رسالة ويرجّع بيانات الغرفة + الرسالة.
     * بعد الإنشاء: إشعار لكل المشاركين في الروم ماعدا المُرسل (FCM + DB).
     */
    public function store(Request $request, string $idOrSlug)
    {
        [$senderTypeShort, $senderId] = $this->currentActorShort();

        $room = $this->findRoom($idOrSlug)
            ->loadCount(['participants', 'messages']);

        $data = $request->validate([
            'message'       => ['nullable','string'],
            'attachments'   => ['nullable','array'],
            'replied_to_id' => ['nullable','integer','exists:chat_messages,id'],
        ]);

        if (empty($data['message']) && empty($data['attachments'])) {
            return response()->json(['message' => 'Message or attachments required.'], 422);
        }

        // لازم يكون المُرسل participant في الروم
        $isParticipant = ChatParticipant::where([
            'room_id'          => $room->id,
            'participant_type' => $senderTypeShort, // short: client|speaker|sponsor
            'participant_id'   => $senderId,
        ])->exists();

        if (!$isParticipant) {
            return response()->json(['message' => 'Sender is not a participant of this room.'], 403);
        }

        // أنشئ الرسالة
        $msg = ChatMessage::create([
            'room_id'       => $room->id,
            'sender_type'   => $senderTypeShort,   // short alias
            'sender_id'     => $senderId,
            'message'       => $data['message'] ?? null,
            'attachments'   => $data['attachments'] ?? null,
            'replied_to_id' => $data['replied_to_id'] ?? null,
        ]);

        // حدّث آخر مقروء للمرسل
        ChatParticipant::where([
            'room_id'          => $room->id,
            'participant_type' => $senderTypeShort,
            'participant_id'   => $senderId,
        ])->update(['last_read_message_id' => $msg->id]);

        // حمّل بيانات العرض
        $room->refresh()->loadCount(['participants', 'messages']);
        $msg->load('sender');

        // ===== إشعارات لكل المشاركين (ماعدا المُرسل)
        $participants = ChatParticipant::where('room_id', $room->id)
            ->get(['participant_type','participant_id']);

        foreach ($participants as $p) {
            if ($p->participant_type === $senderTypeShort && (int)$p->participant_id === (int)$senderId) {
                continue; // تخطّي المُرسل
            }

            $recipient = $this->resolveParticipantModel($p->participant_type, (int)$p->participant_id);
            if (!$recipient) continue;

            $sender = $this->resolveParticipantModel($senderTypeShort, (int)$senderId);

            $this->notifyChatMessage(
                recipient: $recipient,
                room:      $room,
                message:   $msg,
                sender:    $sender
            );
        }

        return response()->json([
            'room'    => (new ChatRoomResource($room))->toArray($request),
            'message' => (new ChatMessageResource($msg))->toArray($request),
        ], 201);
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

    private function currentActorShort(): array
    {
        foreach (['client', 'sanctum', 'speaker', 'sponsor'] as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                $id   = $this->safeUserId($user);

                $short = $this->inferShortFromUser($user, $guard); // client|speaker|sponsor
                return [$short, $id];
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

        // fallback
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
     * يحوّل (short type + id) إلى موديل Eloquent (client/sponsor/speaker) لقراءة fcm_token واسم العرض.
     */
    private function resolveParticipantModel(string $short, int $id): ?object
    {
        // غيّر المسارات حسب مشروعك لو مختلفة
        return match ($short) {
            'client'  => \App\Models\Client::query()->find($id),
            'sponsor' => \App\Models\Sponsor::query()->find($id),
            'speaker' => \App\Models\Speaker::query()->find($id),
            default   => null,
        };
    }

    /**
     * إشعار رسالة شات: يسجّل في notifications + يبعث FCM Legacy (بدون باكدچات)
     */
    private function notifyChatMessage(object $recipient, ChatRoom $room, ChatMessage $message, ?object $sender = null): void
    {
        // subject = ChatRoom
        $subjectType = \App\Models\Chat\ChatRoom::class;
        $subjectId   = (int) $room->id;
        $subjectSlug = is_string($room->slug) ? $room->slug : null;

        // عنوان/نص للإشعار
        $senderName = $this->displayName($sender) ?: 'New message';
        $title = $senderName;
        $body  = (string) ($message->message ?: 'Sent an attachment');

        // deeplink + web
        [$deeplink, $webUrl] = $this->buildDeeplinkAndWeb('chat', $subjectId, $subjectSlug);

        // room kind + booking_meta لو الروم Direct Booking
        [$roomKind, $bookingMeta] = $this->extractBookingMetaFromSlug($subjectSlug);

        // 1) خزّن في جدول notifications
        DB::table('notifications')->insert([
            'notifiable_type' => get_class($recipient),
            'notifiable_id'   => (int) $recipient->getKey(),
            'actor_type'      => $sender ? get_class($sender) : null,
            'actor_id'        => $sender ? (int)$sender->getKey() : null,
            'subject_type'    => $subjectType,
            'subject_id'      => $subjectId,
            'subject_slug'    => $subjectSlug,
            'verb'            => 'message',
            'title'           => $title,
            'body'            => $body,
            'data'            => json_encode([
                'room_kind'    => $roomKind,
                'booking_meta' => $bookingMeta,
                'message_id'   => (int) $message->id,
            ], JSON_UNESCAPED_UNICODE),
            'deeplink'        => $deeplink,
            'web_url'         => $webUrl,
            'delivered_at'    => now(),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // 2) ابعت Push لو عنده fcm_token
        $token = trim((string)($recipient->fcm_token ?? ''));
        if ($token !== '') {
            $this->sendFcmLegacy(
                tokens: [$token],
                payload: [
                    'title' => $title,
                    'body'  => $body,
                    'data'  => array_filter([
                        'deeplink'     => $deeplink,
                        'web_url'      => $webUrl,
                        'subject_type' => $subjectType,
                        'subject_id'   => (string)$subjectId,
                        'subject_slug' => $subjectSlug,
                        'room_kind'    => $roomKind,
                        'booking_meta' => $bookingMeta ? json_encode($bookingMeta) : null,
                        'message_id'   => (string)$message->id,
                    ], fn($v) => $v !== null && $v !== '')
                ]
            );
        }
    }

    /**
     * إرسال FCM Legacy HTTP بدون باكدچات
     */
    private function sendFcmLegacy(array $tokens, array $payload): void
    {
        $serverKey = (string) env('FCM_SERVER_KEY', '');
        if ($serverKey === '') return;

        $tokens = array_values(array_filter(array_unique($tokens)));
        if (empty($tokens)) return;

        $notification = [];
        if (!empty($payload['title']) || !empty($payload['body'])) {
            $notification = [
                'title' => (string)($payload['title'] ?? ''),
                'body'  => (string)($payload['body']  ?? ''),
            ];
        }

        $data = (array)($payload['data'] ?? []);

        $post = [
            'registration_ids' => $tokens,
            'notification'     => $notification,
            'data'             => $data,
            'priority'         => 'high',
        ];

        $ch = curl_init('https://fcm.googleapis.com/fcm/send');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: key='.$serverKey,
            ],
            CURLOPT_POSTFIELDS     => json_encode($post, JSON_UNESCAPED_UNICODE),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
        ]);
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * يبني deeplink + web url موحّدين
     */
    private function buildDeeplinkAndWeb(string $module, int $id, ?string $slug = null): array
    {
        $slugPart = $slug ? "/{$slug}" : '';
        $deeplink = "app://{$module}{$slugPart}?id={$id}";
        $webUrl   = url("/{$module}{$slugPart}".($slug ? '' : "/{$id}"));
        return [$deeplink, $webUrl];
    }

    /**
     * لو slug بالشكل booking-{bid}-client-{cid}-sponsor-{sid} يرجّع (room_kind=booking, booking_meta)
     */
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
        // حاول تلاقي اسم مناسب للعرض
        foreach (['name','full_name','username','title'] as $attr) {
            if (isset($user->{$attr}) && is_string($user->{$attr}) && $user->{$attr} !== '') {
                return $user->{$attr};
            }
        }
        // fallback حسب النوع
        return class_basename($user);
    }
}
