<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Journey;
use App\Models\Dropoff;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Response;

class DropoffControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function store_successful_dropoff()
    {
        // Create a sample Journey record
        $journey = Journey::factory()->create();

        // Send a POST request to the store method
        $response = $this->post('/dropoff', [
            'ID' => $journey->id,
        ]);

        // Assert the response status is 204 No Content
        $response->assertStatus(Response::HTTP_NO_CONTENT);

        // Assert the dropoff record was created in the database
        $this->assertDatabaseHas('dropoffs', [
            'journey_id' => $journey->id,
        ]);

        // Check that the dropoff is linked to the correct journey
        $dropoff = Dropoff::where('journey_id', $journey->id)->first();
        $this->assertNotNull($dropoff);
        $this->assertEquals($journey->id, $dropoff->journey_id);
    }

    #[Test]
    public function store_validation_error()
    {
        // Send a POST request to the store method with invalid data       
        $response = $this->post('/dropoff', [
            'ID' => 'invalid_id',
        ]);

        // Assert the response status is 400 Bad Request
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        // Assert that no dropoff record was created in the database
        $this->assertDatabaseMissing('dropoffs', [
            'journey_id' => 'invalid_id',
        ]);
    }

    #[Test]
    public function store_journey_not_found()
    {
        // Send a POST request to the store method with a non-existent journey ID
        $response = $this->post('/dropoff', [
            'ID' => 9999, // Assuming this id does not exist
        ]);

        // Assert the response status is 404 Not Found
        $response->assertStatus(Response::HTTP_NOT_FOUND);

        // Assert that no dropoff record was created in the database
        $this->assertDatabaseMissing('dropoffs', [
            'journey_id' => 9999,
        ]);
    }

    #[Test]
    public function rejects_invalid_content_type_cars_request() 
    {
        return $this->assertInvalidContentTypeRejected();
    }
}
