<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_api_health_has_security_headers(): void
    {
        $res = $this->getJson('/api/health');

        $res->assertOk();
        $res->assertHeader('X-Frame-Options');
        $res->assertHeader('X-Content-Type-Options', 'nosniff');
        $res->assertHeader('Referrer-Policy');
        // CSP may be disabled via env, but by default it should exist.
        $res->assertHeader('Content-Security-Policy');
    }
}
