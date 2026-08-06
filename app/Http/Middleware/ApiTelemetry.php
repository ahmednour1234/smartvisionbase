<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiTelemetry
{
    /**
     * Lightweight request telemetry.
     * - Adds X-Request-Id + X-Response-Time-Ms
     * - Logs slow requests and 5xx responses
     * - Optional slow query capture (env: LOG_SLOW_QUERIES=1)
     */
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);
        $rid = $request->headers->get('X-Request-Id') ?: bin2hex(random_bytes(8));
        $request->headers->set('X-Request-Id', $rid);

        $slowQueries = [];
        $logSlowQueries = (bool) env('LOG_SLOW_QUERIES', false);
        $slowQueryMs = (int) env('SLOW_QUERY_MS', 250);

        if ($logSlowQueries) {
            // NOTE: Do NOT log bindings (often include PII). Log only the SQL template + timing.
            DB::listen(function ($query) use (&$slowQueries, $slowQueryMs) {
                $ms = (float) ($query->time ?? 0);
                if ($ms >= $slowQueryMs) {
                    if (count($slowQueries) < 10) {
                        $slowQueries[] = [
                            'ms' => $ms,
                            'sql' => (string) ($query->sql ?? ''),
                        ];
                    }
                }
            });
        }

        /** @var Response $response */
        $response = $next($request);

        $ms = (int) round((microtime(true) - $start) * 1000);
        $response->headers->set('X-Request-Id', $rid);
        $response->headers->set('X-Response-Time-Ms', (string) $ms);

        $slowRequestMs = (int) env('SLOW_REQUEST_MS', 900);
        $status = (int) ($response->getStatusCode() ?? 200);
        $shouldLog = ($status >= 500) || ($ms >= $slowRequestMs) || (bool) env('LOG_API_REQUESTS', false);

        if ($shouldLog) {
            $u = $request->user();
            Log::info('api_request', [
                'rid' => $rid,
                'method' => $request->method(),
                'path' => $request->path(),
                'status' => $status,
                'ms' => $ms,
                'ip' => $request->ip(),
                'user_id' => $u?->id,
                'role' => $u?->role,
                'slow_queries' => $slowQueries,
            ]);
        }

        return $response;
    }
}
