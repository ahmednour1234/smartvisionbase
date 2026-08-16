<?php

namespace App\Http\Resources\Event;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

// شات
use App\Models\Chat\ChatRoom;
use App\Models\Chat\ChatParticipant;

class EventScheduleResource extends JsonResource
{
    public function toArray($request)
    {
        $lang = $request->header('Accept-Language', $request->query('lang', 'en'));
        $lang = in_array(strtolower($lang), ['ar','en'], true) ? strtolower($lang) : 'en';

        $title       = $lang === 'ar' ? ($this->title_ar ?? $this->title_en) : ($this->title_en ?? $this->title_ar);
        $description = $lang === 'ar' ? ($this->description_ar ?? $this->description_en) : ($this->description_en ?? $this->description_ar);
        $location    = $lang === 'ar' ? ($this->location_ar ?? $this->location_en) : ($this->location_en ?? $this->location_ar);

        $startIso = $this->start_datetime ? Carbon::parse($this->start_datetime)->toIso8601String() : null;
        $endIso   = $this->end_datetime   ? Carbon::parse($this->end_datetime)->toIso8601String()   : null;

        // ================= Chat Room linking & auth check =================
        [$viewerType, $viewerId] = $this->resolveViewer(); // [type,id] أو [null,null]

        // حاول نلاقي روم حسب schedule_id أولاً
        $room = ChatRoom::query()->where('schedule_id', $this->id)->first();

        // احتياطي: لو مفيش روم بالسكيجوال، جرّب السلاج يبدأ بـ schedule-{id}
        if (!$room) {
            $room = ChatRoom::query()
                ->where('slug', 'like', 'schedule-'.$this->id.'%')
                ->first();
        }

        $chatHasRoom      = (bool) $room;
        $chatRoomId       = $room ? (int) $room->id : null;
        $chatAmParticipant= false;

        if ($room && $viewerType && $viewerId) {
            $chatAmParticipant = ChatParticipant::query()
                ->where('room_id', $room->id)
                ->where('participant_type', $viewerType)
                ->where('participant_id', $viewerId)
                ->exists();
        }

        return [
            'id'             => $this->id,
            'event_id'       => $this->event_id,
            'title'          => $title,
            'description'    => $description,
            'location'       => $location,
            'start_datetime' => $startIso,
            'end_datetime'   => $endIso,
            'date'           => $this->start_datetime ? Carbon::parse($this->start_datetime)->toDateString() : null,

            'max_attendees'  => $this->max_attendees !== null ? (int) $this->max_attendees : null,
            'status'         => $this->status,
            'logo'           => $this->logo,

            'speakers'       => EventScheduleSpeakerResource::collection($this->whenLoaded('speakers', $this->speakers)),
            'created_at'     => optional($this->created_at)->toIso8601String(),
            'updated_at'     => optional($this->updated_at)->toIso8601String(),

            // ====== Chat integration ======
            'chat_has_room'      => $chatHasRoom,             // هل فيه روم أصلاً
            'chat_room_id'       => $chatRoomId,              // ID للروم لو موجود (حتى لو مش Participant)
            'chat_am_participant'=> $chatAmParticipant,       // هل المستخدم الحالي ضمن الروم
            'chat_my_type'       => $viewerType,              // client|sponsor|speaker أو null
        ];
    }

    /**
     * يحاول يستنتج نوع وهوية المستخدم الحالي من Guards المدعومة.
     * return array{0:?string,1:?int} => [client|sponsor|speaker|null, id|null]
     */
    private function resolveViewer(): array
    {
        foreach (['client','sponsor','speaker','sanctum'] as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                $id   = $this->safeUserId($user);
                $type = $this->inferShortFromUser($user, $guard);
                return [$type, $id];
            }
        }
        return [null, null];
    }

    private function inferShortFromUser(object $user, string $guard): ?string
    {
        $allowed = ['client','speaker','sponsor'];

        $base = strtolower(class_basename($user));
        if (in_array($base, $allowed, true)) return $base;

        $candidate = strtolower((string) ($user->type ?? $user->role ?? $guard));
        if (in_array($candidate, $allowed, true)) return $candidate;

        if (($user->role ?? null) === 'sponsor' || ($user->type ?? null) === 'sponsor') return 'sponsor';

        return $guard === 'speaker' ? 'speaker' : ($guard === 'client' ? 'client' : null);
    }

    private function safeUserId(object $user): int
    {
        if (method_exists($user, 'getAuthIdentifier')) return (int) $user->getAuthIdentifier();
        if (method_exists($user, 'getKey'))            return (int) $user->getKey();
        if (property_exists($user, 'id'))              return (int) $user->id;
        return 0;
    }
}
