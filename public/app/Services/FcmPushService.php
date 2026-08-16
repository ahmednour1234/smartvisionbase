<?php
// app/Services/NotificationService.php
namespace App\Services;

use App\Models\SystemNotification;
use App\Support\Deeplink;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    public function __construct(private FcmPushService $fcm) {}

    public function send(
        Model  $notifiable,
        ?Model $actor,
        Model  $subject,
        array  $payload = []
    ): SystemNotification
    {
        $subjectSlug   = $payload['subject_slug'] ?? ($subject->slug ?? null);
        $notifiableType = get_class($notifiable);
        $actorType      = $actor ? get_class($actor) : null;
        $subjectType    = get_class($subject);

        // Build deeplink/web إن مش مبعوتين
        [$deeplink, $web] = $payload['deeplink'] ?? $payload['web_url']
            ? [$payload['deeplink'] ?? null, $payload['web_url'] ?? null]
            : \App\Support\Deeplink::for($this->shortType($subjectType), (int)$subject->getKey(), $subjectSlug);

        $record = SystemNotification::create([
            'notifiable_type' => $notifiableType,
            'notifiable_id'   => (int)$notifiable->getKey(),
            'actor_type'      => $actorType,
            'actor_id'        => $actor ? (int)$actor->getKey() : null,
            'subject_type'    => $subjectType,
            'subject_id'      => (int)$subject->getKey(),
            'subject_slug'    => $subjectSlug,
            'verb'            => $payload['verb']  ?? null,
            'title'           => $payload['title'] ?? null,
            'body'            => $payload['body']  ?? null,
            'data'            => $payload['data']  ?? null,
            'deeplink'        => $deeplink,
            'web_url'         => $web,
            'delivered_at'    => now(),
        ]);

        // إرسال FCM اعتمادًا على fcm_token في الموديل
        $this->fcm->sendToModel($notifiable, [
            'title' => $record->title,
            'body'  => $record->body,
            'deeplink' => $record->deeplink,
            'web_url'  => $record->web_url,
            'data' => [
                'subject_type' => $subjectType,
                'subject_id'   => (int)$subject->getKey(),
                'subject_slug' => $subjectSlug,
                // لو إشعار شات/حجز:
                'room_kind'    => $payload['data']['room_kind']    ?? null,
                'booking_meta' => $payload['data']['booking_meta'] ?? null,
            ],
            'collapse_key' => $payload['collapse_key'] ?? 'inbox',
        ]);

        return $record;
    }

    private function shortType(string $fqcn): string
    {
        $base = class_basename($fqcn); // Booking
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $base)); // booking
    }
}
