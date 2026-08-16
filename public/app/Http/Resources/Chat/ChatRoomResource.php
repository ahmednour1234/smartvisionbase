<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use App\Models\EventSchedule;
use App\Models\Client;
use App\Models\Sponsor;
use App\Models\Booking as BookingModel; // غيّر الاسم إذا كان موديلك مختلف

class ChatRoomResource extends JsonResource
{
    public function toArray($request)
    {
        $messagesLoaded = $this->relationLoaded('messages');

        // ===== Resolve current viewer (prefer injected, else Auth) =====
        [$viewerType, $viewerId] = $this->injectedOrCurrentActor();

        // ===== Defaults =====
        $displayName   = (string)($this->title ?? 'Chat');
        $displayAvatar = null;
        $peer          = null;

        // ===== Detect booking slug =====
        $slug = is_string($this->slug) ? $this->slug : '';
        $isDirectBookingRoom = false;
        $bookingMeta = null;
        $bookingId = null;

        if ($slug !== '' && preg_match('/^booking-(\d+)-client-(\d+)-sponsor-(\d+)$/i', $slug, $m)) {
            $isDirectBookingRoom = true;
            $bookingId = (int)$m[1];
            $bookingMeta = [
                'booking_id' => $bookingId,
                'client_id'  => (int)$m[2],
                'sponsor_id' => (int)$m[3],
            ];
        }

        $roomKind = $isDirectBookingRoom
            ? 'booking'
            : (!empty($this->schedule_id) ? 'schedule' : 'generic');

        // ===== UI naming logic =====
        if (!empty($this->schedule_id) && class_exists(EventSchedule::class) && $roomKind === 'schedule') {
            if ($sch = EventSchedule::find($this->schedule_id)) {
                $displayName   = (string)($sch->title_en ?? $this->title ?? 'Chat');
                $displayAvatar = $sch->logo ?? null;
            }
        } else {
            if ($peerInfo = $this->resolvePeerForNonSchedule($viewerType, $viewerId)) {
                $displayName   = $peerInfo['name']   ?? $displayName;
                $displayAvatar = $peerInfo['avatar'] ?? $displayAvatar;
                $peer          = [
                    'type'   => $peerInfo['type'] ?? null,
                    'id'     => $peerInfo['id']   ?? null,
                    'name'   => $peerInfo['name'] ?? null,
                    'avatar' => $peerInfo['avatar'] ?? null,
                ];
            }
        }

        // ===== last_message =====
        $lastMessageRes = null;
        if ($this->relationLoaded('latestMessage') && $this->latestMessage) {
            $lastMessageRes = new ChatMessageResource($this->latestMessage);
        } elseif ($messagesLoaded && $this->messages->count() > 0) {
            $lastMessageRes = new ChatMessageResource($this->messages->first());
        }

        // ===== viewer_is_participant =====
        $viewerIsParticipant = false;
        if ($viewerType && $viewerId) {
            if ($this->relationLoaded('participants')) {
                $viewerIsParticipant = $this->participants->contains(
                    fn ($p) => $p->participant_type === $viewerType && (int)$p->participant_id === (int)$viewerId
                );
            } else {
                $viewerIsParticipant = \App\Models\Chat\ChatParticipant::query()
                    ->where('room_id', (int)$this->id)
                    ->where('participant_type', $viewerType)
                    ->where('participant_id', (int)$viewerId)
                    ->exists();
            }
        }

        // ===== booking time (only when NOT schedule) =====
        $timeBooking = null;
        if ($roomKind !== 'schedule' && $isDirectBookingRoom && class_exists(BookingModel::class)) {
            $timeBooking = $this->resolveBookingTimeRange($bookingId);
        }

        return [
            'id'                   => (int)$this->id,
            'slug'                 => (string)($this->slug ?? ''),
            'title'                => (string)($this->title ?? ''),
            'schedule_id'          => (int)($this->schedule_id ?? 0),
            'is_active'            => (bool)($this->is_active ?? false),

            'participants_count'   => $this->when(isset($this->participants_count), (int)$this->participants_count, fn () =>
                $this->participants()->count()
            ),
            'messages_count'       => $this->when(isset($this->messages_count), (int)$this->messages_count, fn () =>
                $this->messages()->count()
            ),

            // UI
            'display_name'         => (string)($displayName ?? ''),
            'display_avatar'       => $displayAvatar,
            'peer'                 => $this->when(!is_null($peer), $peer),

            // Meta
            'is_direct_booking_room' => (bool)$isDirectBookingRoom,
            'room_kind'              => (string)$roomKind,
            'booking_meta'           => $this->when($isDirectBookingRoom, $bookingMeta),

            // Viewer
            'viewer' => [
                'type' => $viewerType,
                'id'   => $viewerId,
            ],
            'viewer_is_participant' => (bool)$viewerIsParticipant,

            // Messages
            'messages'             => $this->when($messagesLoaded, fn () =>
                ChatMessageResource::collection($this->messages)
            ),
            'last_message'         => $this->when(!is_null($lastMessageRes), $lastMessageRes),

            // >>> المطلوب: إظهار ميعاد الحجز في المفتاح time_booking (من/إلى) <<<
            'time_booking'         => $this->when(!is_null($timeBooking), $timeBooking),

            'created_at'           => optional($this->created_at)->toISOString(),
            'updated_at'           => optional($this->updated_at)->toISOString(),
        ];
    }

    /* ===================== Helpers ===================== */

    private function injectedOrCurrentActor(): array
    {
        $injectedType = $this->_viewer_type ?? null;
        $injectedId   = $this->_viewer_id   ?? null;
        if ($injectedType && $injectedId) {
            return [(string)$injectedType, (int)$injectedId];
        }
        return $this->currentActorShortOptional();
    }

    /**
     * في الرومات غير الـ Schedule، نجيب "الطرف المقابل" بالنسبة للـ viewer.
     * لو مفيش viewer محقون/مستنتج، هنأخذ أول مشارك.
     */
    private function resolvePeerForNonSchedule(?string $viewerType, ?int $viewerId): ?array
    {
        $p = null;

        if ($this->relationLoaded('participants') && $this->participants) {
            $col = $this->participants;
            if ($viewerType) {
                $col = $col->where('participant_type', '!=', $viewerType)
                           ->when($viewerId, fn($c) => $c->where('participant_id', '!=', $viewerId));
            }
            $p = $col->first() ?: $this->participants->first();
        } else {
            $query = \App\Models\Chat\ChatParticipant::query()
                ->where('room_id', (int)$this->id);

            if ($viewerType) {
                $query->where('participant_type', '!=', $viewerType);
            }
            if ($viewerId) {
                $query->where('participant_id', '!=', $viewerId);
            }
            $p = $query->first();
        }

        if (!$p) return null;

        $peerType = $p->participant_type;
        $peerId   = (int)$p->participant_id;

        $peerName   = null;
        $peerAvatar = null;

        if ($peerType === 'client' && class_exists(Client::class)) {
            if ($c = Client::query()->find($peerId)) {
                $peerName   = (string)($c->name ?? $c->full_name ?? 'Client');
                $peerAvatar = $c->img ?? $c->avatar ?? null;
            }
        } elseif ($peerType === 'sponsor' && class_exists(Sponsor::class)) {
            if ($s = Sponsor::query()->find($peerId)) {
                $peerName   = (string)($s->name_en ?? $s->name ?? 'Sponsor');
                $peerAvatar = $s->image ?? $s->logo ?? null;
            }
        } else {
            $peerName = $this->title ?? 'Chat';
        }

        return [
            'type'   => $peerType,
            'id'     => $peerId,
            'name'   => $peerName,
            'avatar' => $peerAvatar,
        ];
    }

    /** @return array{0:?string,1:?int} */
    private function currentActorShortOptional(): array
    {
        foreach (['client', 'sanctum', 'speaker', 'sponsor'] as $guard) {
            if (Auth::guard($guard)->check()) {
                $user  = Auth::guard($guard)->user();
                $id    = $this->safeUserId($user);
                $short = $this->inferShortFromUser($user, $guard);
                return [$short, $id];
            }
        }
        return [null, null];
    }

    private function inferShortFromUser(object $user, string $guard): string
    {
        $allowed = ['client','speaker','sponsor'];

        $base = strtolower(class_basename($user));
        if (in_array($base, $allowed, true)) return $base;

        $candidate = strtolower((string)($user->type ?? $user->role ?? $guard));
        if (in_array($candidate, $allowed, true)) return $candidate;

        if (($user->role ?? null) === 'sponsor' || ($user->type ?? null) === 'sponsor') return 'sponsor';

        return $guard === 'speaker' ? 'speaker' : 'client';
    }

    private function safeUserId(object $user): int
    {
        if (method_exists($user, 'getAuthIdentifier')) return (int)$user->getAuthIdentifier();
        if (method_exists($user, 'getKey'))            return (int)$user->getKey();
        if (property_exists($user, 'id'))              return (int)$user->id;
        abort(401, 'Cannot resolve user id');
    }

    /**
     * استرجاع فترة الحجز من جدول bookings
     * - يتعامل مع time_from أو حتى time_form (لو العمود مسمى بالخطأ)
     * - يرجع مصفوفة: ['from' => ISO8601|null, 'to' => ISO8601|null]
     */
    private function resolveBookingTimeRange(?int $bookingId): ?array
    {
        if (!$bookingId) return null;

        /** @var \App\Models\Booking|null $booking */
        $booking = BookingModel::query()->find($bookingId);
        if (!$booking) return null;

        // دعم time_from أو time_form
        $from = $booking->time_from ?? $booking->time_form ?? null;
        $to   = $booking->time_to   ?? null;

        // حاول تحويلهم لتواريخ ISO، وإلا رجّع قيمهم كما هي
        $fromIso = null;
        $toIso   = null;

        try { $fromIso = $from ? \Illuminate\Support\Carbon::parse($from)->toISOString() : null; } catch (\Throwable $e) {}
        try { $toIso   = $to   ? \Illuminate\Support\Carbon::parse($to)->toISOString()   : null; } catch (\Throwable $e) {}

        return [
            'from' => $fromIso ?? ($from ?: null),
            'to'   => $toIso   ?? ($to   ?: null),
        ];
    }
}
