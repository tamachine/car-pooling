<?php

namespace Database\Factories;

use App\Models\Journey;
use Illuminate\Database\Eloquent\Factories\Factory;

class JourneyFactory extends Factory
{
    protected $model = Journey::class;

    public function definition()
    {
        return [
            'car_id' => null,
            'people' => $this->faker->numberBetween(1, 6),
        ];
    }
}
