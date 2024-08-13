<?php

namespace App\Services;

use App\Models\Car;
use App\Models\Journey;
use DB;

/**
 * Service to handle car pooling for journeys.
 *
 * This service is responsible for assigning available cars to journeys based on
 * the number of people in the journey and the availability of cars. It performs
 * checks to ensure that journeys are assigned cars in a way that respects priority
 * and availability constraints. It uses transactions to maintain data integrity during
 * the car assignment process.
 */
class CarPooling
{
    /**
     * The journey that needs a car assignment.
     *
     * @var Journey
     */
    public $journey;
    
    public $availableCar;

    /**
     * Assign a car to the given journey based on availability.    
     *
     * @param Journey $journey
     * @return bool
     */
    public function pool(Journey $journey)
    {
        $this->journey = $journey;
        
        // If the journey has already been dropped off, no car needs to be assigned.
        if ($this->journeyIsNotDroppedoff()) {
            
            // If there is no priority journey that requires attention, proceed with car assignment.
            if (!$this->thereIsAPriorityJourney()) {
              
                // Attempt to find the first available car that meets the seat requirements.
                $car = $this->getFirstAvailableCar($this->journey->people);

                if ($car) {
                    
                    // Use a transaction to safely assign the car to the journey
                    DB::transaction(function () use ($car) {
                        $this->assignCar($car);
                    });

                    return true;
        
                }
            } 
            
        }
        
        return false;
        
    }

    /**
     * Check if the journey has not been dropped off.    
     *
     * @return bool
     */
    protected function journeyIsNotDroppedoff()
    {
        return !$this->journey->dropoff; 
    }

    /**
     * Determine if there are any priority journeys that need attention first.
     *          
     * @return bool
     */
    protected function thereIsAPriorityJourney()
    {        
        $date = $this->journey->created_at;  // Get the creation date of the current journey

        // Retrieve journeys created before the current journey that have not been assigned a car
        // and have not been marked as dropped off. Limit the result to 100 for performance reasons.
        $journeysNotAssignedYet = Journey::withoutCar()
            ->withoutDropoff()
            ->createdBefore($date)
            ->orderBy('created_at', 'asc')
            ->limit(100)
            ->get();
                
        foreach($journeysNotAssignedYet as $journey) {

            // Check if there is at least one car with available seats that can accommodate the number of people in the journey
            if(Car::withAvailableSeats($journey->people)->count() > 0) {
                return true;  // There is at least one priority journey with an available car
            }
        }

        return false;  // No priority journeys or no available cars found
    }

    /**
     * Retrieve the first available car that has enough seats.    
     *
     * @param int $people
     * @return Car|null
     */
    protected function getFirstAvailableCar($people)
    {
        // Find the first car with enough available seats for the given number of people.
        return Car::withAvailableSeats($people)->first();
    }

    /**
     * Assign the given car to the current journey.     
     *
     * @param Car $car
     * @return void
     */
    protected function assignCar(Car $car)
    {
        $this->journey->car_id = $car->id;
        $this->journey->save();
    }
}
