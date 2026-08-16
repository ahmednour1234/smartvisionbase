<?php
// app/Http/Controllers/API/Chat/Concerns/ParticipantMap.php
namespace App\Http\Controllers\API\Chat\Concerns;

class ParticipantMap
{
    public static function toClass(string $short): ?string
    {
        $short = strtolower($short);
        return match ($short) {
            'client'  => \App\Models\Client::class,
            'sponsor' => \App\Models\Sponsor::class,
            'speaker' => \App\Models\Speaker::class,
            default   => null,
        };
    }

    public static function toShort(?string $class): ?string
    {
        if (!$class) return null;
        return match ($class) {
            \App\Models\Client::class  => 'client',
            \App\Models\Sponsor::class => 'sponsor',
            \App\Models\Speaker::class => 'speaker',
            default => null,
        };
    }
}
