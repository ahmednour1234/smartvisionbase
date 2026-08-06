<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLocaleFromUrl
{
    public function handle($request, Closure $next)
    {
        $segment = $request->segment(1);

        if (in_array($segment, ['en', 'ar'])) {
            App::setLocale($segment);
            app()->setLocale($segment);
        } else {
            App::setLocale(config('app.locale'));
        }

        return $next($request);
    }
}
