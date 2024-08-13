<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Car;
use App\Models\Journey;
use App\Models\Dropoff;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\Test;

class CarControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function update_cars_with_valid_data()
    {
        // Define valid data for updating cars
        $data = [
            ['id' => 1, 'seats' => 4],
            ['id' => 2, 'seats' => 6],
        ];

        // Simulate the PUT request to the /cars endpoint
        $response = $this->putJson('/cars', $data);

        // Assert that the response status is 200 OK
        $response->assertStatus(Response::HTTP_OK);
        
        // Assert that the cars have been created in the database
        $this->assertDatabaseHas('cars', ['id' => 1, 'seats' => 4]);
        $this->assertDatabaseHas('cars', ['id' => 2, 'seats' => 6]);
    }   

    #[Test]
    public function application_state_reset()
    {
        // Create some initial data
        Car::factory()->create(['id' => 1, 'seats' => 4]);
        Journey::factory()->create(['id' => 1, 'car_id' => 1]);
        Dropoff::factory()->create(['journey_id' => 1]);

        // Define valid data for updating cars
        $data = [
            ['id' => 2, 'seats' => 6],
        ];

        // Simulate the PUT request to the /cars endpoint
        $response = $this->putJson('/cars', $data);

        // Assert that the response status is 200 OK
        $response->assertStatus(Response::HTTP_OK);

        // Assert that all initial data was reset
        $this->assertDatabaseMissing('cars', ['id' => 1]);
        $this->assertDatabaseMissing('journeys', ['id' => 1]);
        $this->assertDatabaseMissing('dropoffs', ['journey_id' => 1]);

        // Assert that the new data has been created
        $this->assertDatabaseHas('cars', ['id' => 2, 'seats' => 6]);
    }

    #[Test]
    public function update_cars_with_invalid_data()
    {
        // Define invalid data (e.g., missing required fields)
        $data = [
            ['id' => 'not-an-integer', 'seats' => 'invalid-seats'],
            ['id' => 2], // Missing 'seats'
        ];

        // Simulate the PUT request to the /cars endpoint
        $response = $this->putJson('/cars', $data);

        // Assert that the response status is 400 Bad Request
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    #[Test]
    public function update_cars_with_empty_data()
    {
        // Simulate the PUT request to the /cars endpoint with empty data
        $response = $this->putJson('/cars', []);

        // Assert that the response status is 400 Bad Request
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson([
            'error' => 'Invalid data format: The data should be a non-empty array'
        ]);
    }

    #[Test]
    public function update_cars_with_null_data()
    {
        // Simulate the PUT request to the /cars endpoint with null data
        $response = $this->putJson('/cars');

        // Assert that the response status is 400 Bad Request
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson([
            'error' => 'Invalid data format: The data should be a non-empty array'
        ]);
    }
}
