<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_returns_429_after_sixty_requests(): void
    {
        RateLimiter::clear('test-rate-limit');

        for ($i = 0; $i < 60; $i++) {
            $this->getJson('/api/products');
        }

        $response = $this->getJson('/api/products');

        $response->assertStatus(429);

        RateLimiter::clear('test-rate-limit');
    }
}