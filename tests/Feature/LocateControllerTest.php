<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use App\Models\Car;
use App\Models\Journey;
use App\Models\Dropoff;
use PHPUnit\Framework\Attributes\Test;

class LocateControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function locate_journey_with_car()
    {
        // Create a car and a journey
        $car = Car::factory()->create(['seats' => 4]);
        $journey = Journey::factory()->create(['car_id' => $car->id]);

        // Make a request to the locate endpoint
        $response = $this->postJson('/locate', ['ID' => $journey->id]);

        // Assert that the response status is 200 OK
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'id' => $car->id,
            'seats' => $car->seats,
        ]);
    }

    #[Test]
    public function locate_journey_without_car()
    {
        // Create a journey without an assigned car
        $journey = Journey::factory()->create(['car_id' => null]);

        // Make a request to the locate endpoint
        $response = $this->postJson('/locate', ['ID' => $journey->id]);

        // Assert that the response status is 204 No Content
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    #[Test]
    public function locate_with_invalid_data()
    {
        // Make a request to the locate endpoint with invalid data
        $response = $this->postJson('/locate', ['ID' => 'invalid']);

        // Assert that the response status is 400 Bad Request
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    #[Test]
    public function locate_non_existing_journey()
    {
        // Make a request to the locate endpoint with a non-existing journey ID
        $response = $this->postJson('/locate', ['ID' => 999]);

        // Assert that the response status is 404 Not Found
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    #[Test]
    public function locate_journey_with_dropoff()
    {
        // Create a journey and mark it as dropoff
        $journey = Journey::factory()->create();
        Dropoff::factory()->create(['journey_id' => $journey->id]);

        // Make a request to the locate endpoint
        $response = $this->postJson('/locate', ['ID' => $journey->id]);

        // Assert that the response status is 404 Not Found
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    #[Test]
    public function rejects_invalid_content_type_cars_request() 
    {
        return $this->assertInvalidContentTypeRejected();
    }
}
