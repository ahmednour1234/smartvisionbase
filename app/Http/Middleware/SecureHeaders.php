<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Basic hardening (safe defaults for Filament).
        $response->headers->set('X-Frame-Options', env('SECURE_HEADERS_FRAME_OPTIONS', 'SAMEORIGIN'));
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', env('SECURE_HEADERS_REFERRER_POLICY', 'strict-origin-when-cross-origin'));

        // Modern isolation controls (keep conservative to avoid breaking third‑party embeds).
        $response->headers->set('Cross-Origin-Opener-Policy', env('SECURE_HEADERS_COOP', 'same-origin'));
        $response->headers->set('Cross-Origin-Resource-Policy', env('SECURE_HEADERS_CORP', 'same-site'));

        // Permissions Policy (lock down sensitive APIs).
        $response->headers->set(
            'Permissions-Policy',
            env('SECURE_HEADERS_PERMISSIONS_POLICY', 'geolocation=(), microphone=(), camera=(), payment=(), usb=(), interest-cohort=()')
        );

        // HSTS (only when HTTPS).
        $enableHsts = filter_var(env('SECURE_HEADERS_ENABLE_HSTS', true), FILTER_VALIDATE_BOOL);
        if ($enableHsts && $request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', env(
                'SECURE_HEADERS_HSTS',
                'max-age=31536000; includeSubDomains; preload'
            ));
        }

        // CSP (Filament uses some inline styles/scripts; default policy keeps it working.
        // If you want strict CSP with nonces, set SECURE_HEADERS_CSP to a nonce-based policy and enable nonces app-wide.)
        $enableCsp = filter_var(env('SECURE_HEADERS_ENABLE_CSP', true), FILTER_VALIDATE_BOOL);
        if ($enableCsp) {
            $csp = env('SECURE_HEADERS_CSP',
                "default-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'self'; object-src 'none'; " .
                "img-src 'self' data: https:; font-src 'self' data: https:; " .
                "style-src 'self' 'unsafe-inline' https:; script-src 'self' 'unsafe-inline' https:; " .
                "connect-src 'self' https: wss:; frame-src 'self' https:;"
            );

            $reportOnly = filter_var(env('SECURE_HEADERS_CSP_REPORT_ONLY', false), FILTER_VALIDATE_BOOL);
            $headerName = $reportOnly ? 'Content-Security-Policy-Report-Only' : 'Content-Security-Policy';
            $response->headers->set($headerName, $csp);
        }

        return $response;
    }
}
