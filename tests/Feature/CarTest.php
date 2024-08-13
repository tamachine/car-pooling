<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Car;
use App\Models\Journey;
use App\Models\Dropoff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class CarTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_car_has_many_journeys()
    {
        // Create a car
        $car = Car::factory()->create();

        // Create journeys associated with the car
        $journey1 = Journey::factory()->create(['car_id' => $car->id]);
        $journey2 = Journey::factory()->create(['car_id' => $car->id]);

        // Assert that the car has the journeys
        $this->assertTrue($car->journeys->contains($journey1));
        $this->assertTrue($car->journeys->contains($journey2));
    }

    #[Test]
    public function it_can_scope_cars_with_available_seats()
    {
        // Create a car with 5 seats
        $car = Car::factory()->create(['seats' => 5]);

        // Create a journey with 2 people assigned to the car
        Journey::factory()->create(['car_id' => $car->id, 'people' => 2]);

        // Test when the car has enough available seats (3 remaining)
        $this->assertTrue(Car::withAvailableSeats(3)->exists());

        // Test when the car doesn't have enough available seats (4 required)
        $this->assertFalse(Car::withAvailableSeats(4)->exists());
    }

    #[Test]
    public function it_excludes_dropoffs_from_available_seats_calculation()
    {
        // Create a car with 5 seats
        $car = Car::factory()->create(['seats' => 5]);

        // Create a journey with 2 people assigned to the car
        $journey = Journey::factory()->create(['car_id' => $car->id, 'people' => 2]);

        // Mark the journey as dropped off
        Dropoff::factory()->create(['journey_id' => $journey->id]);

        // Since the journey was dropped off, the car should have all 5 seats available
        $this->assertTrue(Car::withAvailableSeats(5)->exists());
    }
}
