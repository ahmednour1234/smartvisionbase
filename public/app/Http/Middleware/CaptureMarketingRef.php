<?php
// app/Http/Middleware/CaptureMarketingRef.php
namespace App\Http\Middleware;

use App\Models\MarketingRef;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureMarketingRef
{
    public function handle(Request $request, Closure $next): Response
    {
        $code = $request->query('ref');
        if ($code) {
            $ref = MarketingRef::where('code', $code)->first();
            if ($ref && $ref->isUsableForPath($request->path())) {
                // سجّل hit خفيف
                try {
                    $ref->hits()->create([
                        'ip'         => substr($request->ip() ?? '', 0, 64),
                        'user_agent' => substr($request->userAgent() ?? '', 0, 255),
                        'path'       => '/'.$request->path(),
                    ]);
                } catch (\Throwable $e) {}
                // خزّن الكود في الكوكي 7 أيام
                cookie()->queue(cookie('mr_code', $code, 60*24*7));
                // أو بالسيشن: session(['mr_code' => $code]);
            }
        }
        return $next($request);
    }
}
