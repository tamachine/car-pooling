<?php

// tests/Feature/JourneyFeatureTest.php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Journey;
use App\Models\Car;
use App\Models\Dropoff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class JourneyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_journey_can_be_created_and_linked_to_a_car_and_dropoff()
    {
        // Create Car and Journey instances
        $car = Car::factory()->create();
        $journey = Journey::factory()->create(['car_id' => $car->id]);

        // Create a Dropoff linked to the Journey
        $dropoff = Dropoff::factory()->create(['journey_id' => $journey->id]);

        // Assert that the Journey is correctly saved and linked to the Car and Dropoff
        $this->assertDatabaseHas('journeys', ['id' => $journey->id, 'car_id' => $car->id]);
        $this->assertDatabaseHas('dropoffs', ['id' => $dropoff->id, 'journey_id' => $journey->id]);
    }

    #[Test]
    public function it_scopes_journeys_without_dropoff()
    {
        // Create a Journey without a Dropoff
        $journeyWithoutDropoff = Journey::factory()->create();

        // Create a Journey with a Dropoff
        $journeyWithDropoff = Journey::factory()->create();
        Dropoff::factory()->create(['journey_id' => $journeyWithDropoff->id]);

        // Assert that only the journey without a dropoff is returned by the scope
        $this->assertEquals(1, Journey::withoutDropoff()->count());
        $this->assertTrue(Journey::withoutDropoff()->first()->is($journeyWithoutDropoff));
    }

    #[Test]
    public function it_scopes_journeys_without_car()
    {
        // Create a Journey without a Car
        $journeyWithoutCar = Journey::factory()->create(['car_id' => null]);

        // Create a Journey with a Car
        Journey::factory()->create(['car_id' => Car::factory()->create()->id]);

        // Assert that only the journey without a car is returned by the scope
        $this->assertEquals(1, Journey::withoutCar()->count());
        $this->assertTrue(Journey::withoutCar()->first()->is($journeyWithoutCar));
    }

    #[Test]
    public function it_scopes_journeys_created_before_a_given_date()
    {
        $date = now();

        // Create a Journey before the given date
        $journeyBeforeDate = Journey::factory()->create(['created_at' => $date->subDay()]);

        // Create a Journey after the given date
        Journey::factory()->create(['created_at' => $date->addDay()]);

        // Assert that only the journey created before the given date is returned by the scope
        $this->assertEquals(1, Journey::createdBefore(now())->count());
        $this->assertTrue(Journey::createdBefore(now())->first()->is($journeyBeforeDate));
    }
}
