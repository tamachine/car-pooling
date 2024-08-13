<?php 

namespace Database\Factories;

use App\Models\Dropoff;
use App\Models\Journey;
use Illuminate\Database\Eloquent\Factories\Factory;

class DropoffFactory extends Factory
{
    protected $model = Dropoff::class;

    public function definition()
    {
        return [
            'journey_id' => Journey::factory(),
        ];
    }
}
