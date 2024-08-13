<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Dropoff;
use App\Models\Journey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class DropoffTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_dropoff_can_be_created_and_linked_to_a_journey()
    {
        // Create a Journey instance
        $journey = Journey::factory()->create();

        // Create a Dropoff instance linked to the above Journey
        $dropoff = Dropoff::factory()->create(['journey_id' => $journey->id]);

        // Check if the Dropoff is correctly saved in the database with the correct journey_id
        $this->assertDatabaseHas('dropoffs', [
            'id' => $dropoff->id,
            'journey_id' => $journey->id,
        ]);
    }

    #[Test]
    public function it_fetches_the_correct_journey_for_a_dropoff()
    {
        // Create a Journey instance
        $journey = Journey::factory()->create();

        // Create a Dropoff instance linked to the above Journey
        $dropoff = Dropoff::factory()->create(['journey_id' => $journey->id]);

        // Assert that the Dropoff correctly fetches its associated Journey
        $this->assertEquals($journey->id, $dropoff->journey->id);
    }
}
