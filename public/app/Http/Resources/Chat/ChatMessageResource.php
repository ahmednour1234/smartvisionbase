<?php

namespace App\Http\Resources\Chat;

use App\Support\UserNameResolver;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;

class ChatMessageResource extends JsonResource
{
    public function toArray($request)
    {
        $lang = $request->header('Accept-Language', $request->query('lang', 'en'));
        $lang = in_array(strtolower($lang), ['ar', 'en'], true) ? strtolower($lang) : 'en';

        // ===== Resolve stored type (alias/FQCN) -> alias short
        $storedType  = (string) $this->sender_type; // alias or FQCN
        $senderShort = $this->toShortAlias($storedType);

        // ===== Eager-loaded relation (preferred)
        $senderModel = $this->whenLoaded('sender', fn () => $this->sender);

        // ===== Lazy load fallback if not eager-loaded
        if (!$senderModel) {
            $fqcn = $this->toFqcn($storedType);
            if ($fqcn && class_exists($fqcn) && method_exists($fqcn, 'query')) {
                $senderModel = $fqcn::query()->find($this->sender_id);
            }
        }

        // ===== Resolve current viewer (auth) -> { type, id }
        [$viewerType, $viewerId] = $this->currentActorShortOptional();
        $senderIsMe = $viewerType !== null
            ? ($senderShort === $viewerType && (int) $this->sender_id === (int) $viewerId)
            : false;

        return [
            'id'            => (int) $this->id,
            'room_id'       => (int) $this->room_id,

            // sender info (raw)
            'sender_type'   => $senderShort,
            'sender_id'     => (int) $this->sender_id,

            // viewer/auth info (اللي طالب الـAPI)
            'viewer' => [
                'type' => $viewerType,
                'id'   => $viewerId !== null ? (int) $viewerId : null,
            ],
            'sender_is_me'  => (bool) $senderIsMe,

            'sender_name'   => UserNameResolver::name($this->toFqcn($storedType) ?: $senderShort, (int) $this->sender_id, $lang),
            'message'       => $this->message,
            'attachments'   => $this->attachments ?: [],
            'replied_to_id' => $this->replied_to_id ? (int) $this->replied_to_id : null,
            'created_at'    => optional($this->created_at)->toISOString(),

            // sender expanded data via proper Resource
            'sender'        => [
                'type' => $senderShort,
                'id'   => (int) $this->sender_id,
                'data' => $this->when($senderModel !== null, function () use ($senderShort, $senderModel) {
                    switch ($senderShort) {
                        case 'client':
                            return new \App\Http\Resources\Client\ClientResource($senderModel);
                        case 'speaker':
                            return new \App\Http\Resources\Speaker\SpeakerResource($senderModel);
                        case 'sponsor':
                            return new \App\Http\Resources\Sponsor\SponsorResource($senderModel);
                        default:
                            return null;
                    }
                }),
            ],
        ];
    }

    /**
     * Convert stored type (alias or FQCN) to a short alias: client|speaker|sponsor.
     */
    private function toShortAlias(string $storedType): string
    {
        // If it's an alias present in morph map, keep it
        $mapped = Relation::getMorphedModel($storedType);
        if ($mapped !== null) {
            return strtolower($storedType);
        }

        // FQCN -> alias (via reverse map or basename)
        if (class_exists($storedType)) {
            $reverse = $this->reverseMorphMap();
            foreach ($reverse as $alias => $fqcn) {
                if ($fqcn === $storedType) return $alias;
            }
            return strtolower(class_basename($storedType));
        }

        // fallback
        return strtolower($storedType);
    }

    /**
     * Convert stored type (alias or FQCN) to FQCN using morph map or guessing.
     */
    private function toFqcn(string $storedType): ?string
    {
        // Alias -> FQCN
        $mapped = Relation::getMorphedModel($storedType);
        if ($mapped && class_exists($mapped)) {
            return $mapped;
        }

        // Already FQCN
        if (class_exists($storedType)) {
            return $storedType;
        }

        // Guess common namespaces from alias
        $studly = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $storedType)));
        $guesses = [
            "App\\Models\\{$studly}",
            "App\\Models\\Chat\\{$studly}",
            "App\\{$studly}",
        ];
        foreach ($guesses as $fqcn) {
            if (class_exists($fqcn)) return $fqcn;
        }

        return null;
    }

    /**
     * Reverse morph map (alias => FQCN). If not set, returns only known aliases that are configured.
     */
    private function reverseMorphMap(): array
    {
        $aliases = ['client', 'speaker', 'sponsor'];
        $map = [];
        foreach ($aliases as $alias) {
            $fqcn = Relation::getMorphedModel($alias);
            if ($fqcn) $map[$alias] = $fqcn;
        }
        return $map;
    }

    /**
     * Resolve current viewer (auth) across multiple guards.
     * Returns [type|null, id|null]
     */
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
}
