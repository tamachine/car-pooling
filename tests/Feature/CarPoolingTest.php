<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\CarPooling;
use App\Models\Car;
use App\Models\Journey;
use App\Models\Dropoff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class CarPoolingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_assigns_car_to_journey_if_available_and_not_priority()
    {
        // Create a journey and a car
        $journey = Journey::factory()->create(['people' => 4]);
        $car = Car::factory()->create(['seats' => 5]);

         // Try to assign a car to the regular journey
        $carPooling = new CarPooling();
        $result = $carPooling->pool($journey);

        // Check that the car was assigned successfully
        $this->assertTrue($result);
        $this->assertEquals($car->id, $journey->car_id);
    }

    #[Test]
    public function it_does_not_assign_car_if_journey_has_been_dropped_off()
    {
        // Create a journey and a dropoff
        $journey = Journey::factory()->create(['id' => 1,'people' => 4]);
        Dropoff::factory()->create(['journey_id' => $journey->id]); 
        Car::factory()->create(['seats' => 5]);        

        // Try to assign a car to the regular journey
        $carPooling = new CarPooling();
        $result = $carPooling->pool($journey);

        // Assert that no car was assigned
        $this->assertFalse($result);
        $this->assertNull($journey->refresh()->car_id); // Use refresh to obtain the more recent model state
    }

    #[Test]
    public function it_does_not_assign_car_if_there_is_a_priority_journey()
    {
        // Create a priority journey and a regular journey
        $priorityJourney = Journey::factory()->create(['people' => 4, 'created_at' => now()->subDay()]);
        $regularJourney = Journey::factory()->create(['people' => 4]);
        Car::factory()->create(['seats' => 5]);

        // Try to assign a car to the regular journey
        $carPooling = new CarPooling();
        $result = $carPooling->pool($regularJourney);

        // Assert that the regular journey was not assigned a car
        $this->assertFalse($result);
        $this->assertNull($regularJourney->car_id);

        // Ensure the priority journey has not been assigned a car
        $this->assertNull($priorityJourney->car_id);
    }

    #[Test]
    public function it_does_not_assign_car_if_no_available_cars()
    {
        // Create a journey with no available cars
        $journey = Journey::factory()->create(['people' => 4]);

        // Try to assign a car to the journey
        $carPooling = new CarPooling();
        $result = $carPooling->pool($journey);

        // Assert that no car was assigned
        $this->assertFalse($result);
        $this->assertNull($journey->car_id);
    }
}
