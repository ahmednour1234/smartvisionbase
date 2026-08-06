<?php

namespace Tests\Feature;

use Tests\TestCase;

class SmokeTest extends TestCase
{
    public function test_root_redirects_to_admin(): void
    {
        $response = $this->get('/');
        $response->assertStatus(302);
    }
}
