<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $lang = $request->query('lang');

        if (! $lang) {
            $lang = $request->session()->get('lang');
        }

        if (! $lang) {
            // Use the first language in the Accept-Language header (e.g., "ar", "en").
            $accept = $request->header('Accept-Language');
            if (is_string($accept) && $accept !== '') {
                $lang = strtolower(substr(trim($accept), 0, 2));
            }
        }

        if (in_array($lang, ['ar', 'en'], true)) {
            app()->setLocale($lang);

            // Persist only for web requests (Filament uses sessions).
            if ($request->hasSession()) {
                $request->session()->put('lang', $lang);
            }
        }

        return $next($request);
    }
}
