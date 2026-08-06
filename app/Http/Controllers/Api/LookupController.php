<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Event;
use App\Models\Package;
use App\Models\LostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LookupController extends Controller
{
    public function all(Request $request)
{
    $user = $request->user();

    $refresh = $request->query('refresh');
    $refresh = $refresh === '1' || $refresh === 1 || $refresh === true || $refresh === 'true';
    if ($refresh && (! $user || ! method_exists($user, 'isAdmin') || ! $user->isAdmin())) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    $cacheKey = (string) config('crm.lookups.cache_key');
    $ttlSeconds = (int) config('crm.lookups.cache_ttl');

    if ($refresh) {
        Cache::forget($cacheKey);
    }

    $cacheHit = Cache::has($cacheKey);

    $payload = Cache::remember($cacheKey, $ttlSeconds, function () {
        return [
            'countries' => Country::where('is_active', 1)
                ->orderBy('sort_order')->orderBy('name')
                ->get(['id', 'iso2', 'name']),
            'events' => Event::where('is_active', 1)
                ->orderBy('sort_order')->orderBy('name')
                ->get(['id', 'name', 'location', 'event_date_from', 'event_date_to']),
            'packages' => Package::where('is_active', 1)
                ->orderBy('sort_order')->orderBy('name')
                ->get(['id', 'name']),
            'lost_categories' => LostCategory::where('is_active', 1)
                ->orderBy('sort_order')->orderBy('name')
                ->get(['id', 'name']),
        ];
    });

    $resp = response()->json($payload);
    $resp->headers->set('X-Lookups-Cache', $cacheHit ? 'HIT' : 'MISS');

    return $resp;
}

}
