<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class StatusControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function status_endpoint()
    {
        // Make a GET request to the status endpoint
        $response = $this->getJson('/status');

        // Assert that the response status is 200 OK
        $response->assertStatus(Response::HTTP_OK);

    }
}
