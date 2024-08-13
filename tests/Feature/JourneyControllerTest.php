<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use App\Models\Journey;
use App\Jobs\AssignCarToJourney;
use PHPUnit\Framework\Attributes\Test;

class JourneyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Clear any jobs queued before each test
        Queue::fake();
    }

    #[Test]
    public function store_valid_journey()
    {
        // Simulate a request to the /api/journey endpoint with valid data
        $response = $this->postJson('/api/journey', [
            'id' => 1,
            'people' => 4,
        ]);

        // Assert that the response status is 200 OK
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the job was dispatched
        Queue::assertPushed(AssignCarToJourney::class);
    }

    #[Test]
    public function store_journey_with_existing_id()
    {
        // Create an existing journey
        Journey::create([
            'id' => 1,
            'people' => 4,
        ]);

        // Simulate a request to the /api/journey endpoint with the same ID
        $response = $this->postJson('/api/journey', [
            'id' => 1,
            'people' => 4,
        ]);

        // Assert that the response status is 400 Bad Request
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    #[Test]
    public function store_journey_with_invalid_data()
    {
        // Simulate a request to the /api/journey endpoint with invalid data
        $response = $this->postJson('/api/journey', []);

        // Assert that the response status is 400 Bad Request
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }
}
