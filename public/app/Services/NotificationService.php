<?php
// app/Services/NotificationService.php
namespace App\Services;

use App\Models\SystemNotification;
use App\Support\Deeplink;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    /**
     * إنشاء إشعار مخصص.
     */
    public function send(
        Model  $notifiable,            // المستلِم (client/sponsor/...)
        ?Model $actor,                 // الفاعل (اختياري)
        Model  $subject,               // الهدف (Booking/Order/...)
        array  $payload = []           // [title, body, verb, data, subject_slug?, deeplink?, web_url?]
    ): SystemNotification
    {
        $subjectSlug = $payload['subject_slug'] ?? ($subject->slug ?? null);

        // حدد morph keys (لو مستخدم MorphMap استخدم keys بتاعته)
        $notifiableType = $this->morphType($notifiable);
        $actorType      = $actor ? $this->morphType($actor) : null;
        $subjectType    = $this->morphType($subject);

        // deeplink/web
        [$deeplink, $web] = $payload['deeplink'] ?? $payload['web_url']
            ? [$payload['deeplink'] ?? null, $payload['web_url'] ?? null]
            : Deeplink::for($this->shortType($subjectType), (int)$subject->getKey(), $subjectSlug);

        return SystemNotification::create([
            'notifiable_type' => $notifiableType,
            'notifiable_id'   => (int) $notifiable->getKey(),
            'actor_type'      => $actorType,
            'actor_id'        => $actor ? (int)$actor->getKey() : null,
            'subject_type'    => $subjectType,
            'subject_id'      => (int) $subject->getKey(),
            'subject_slug'    => $subjectSlug,
            'verb'            => $payload['verb']  ?? null,
            'title'           => $payload['title'] ?? null,
            'body'            => $payload['body']  ?? null,
            'data'            => $payload['data']  ?? null,
            'deeplink'        => $deeplink,
            'web_url'         => $web,
            'delivered_at'    => now(),   // لو هتستخدم Push فعلها بعد نجاح الإرسال
        ]);
    }

    private function morphType(Model $m): string
    {
        // لو معرف MorphMap: return array_search(get_class($m), Relation::morphMap()) ?: get_class($m)
        return get_class($m);
    }

    // حوّل FQCN لاسم قصير بسيط للـ deeplink/web
    private function shortType(string $fqcn): string
    {
        $base = class_basename($fqcn); // Booking
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $base)); // booking
    }
}
