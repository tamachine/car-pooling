<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Car;
use App\Models\Dropoff;

class LocateController extends Controller
{

    protected $car;

    /**
     * Handle the locate request.
     * 
     * This method processes the incoming request to locate a journey and possibly assign a car to it.
     * 
     * @param \Illuminate\Http\Request $request The incoming request instance.
     * @return \Illuminate\Http\Response The response indicating success or failure.
     */
    public function locate(Request $request)
    {                      
        $response = $this->verifyContentType($request, self::CONTENT_TYPE_APPLICATION_FORM);
        if ($response) return $response;
                 
        $this->setData($request->all());

        $validator = $this->validateData();
        if ($validator) return $validator;        

        $validateJourney = $this->validateJourney();
        if ($validateJourney) return $validateJourney;   
        
        $validateDropoff = $this->validateDropoff();
        if ($validateDropoff) return $validateDropoff;   
       
        $validateCar = $this->validateCar();
        if ($validateCar) return $validateCar;    
        
        return response()->json([
            'id'    => $this->car->id,
            'seats' => $this->car->seats,
         ], Response::HTTP_OK);
    }

      /**
     * Validate that the journey exists.
     * 
     * @return \Illuminate\Http\Response|null The response indicating the journey was not found, or null if found.
     */
    protected function validateCar()
    {                      
        $this->setCar($this->journey->car_id);

        if(!$this->car) return response()->noContent(Response::HTTP_NO_CONTENT);        
          
        return null;
    }

    /**
     * Validate that the journey exists.
     * 
     * @return \Illuminate\Http\Response|null The response indicating the journey was not found, or null if found.
     */
    protected function validateJourney()
    {        
        $this->setJourney($this->data['ID']);

        if (!$this->journey) return response()->noContent(Response::HTTP_NOT_FOUND); 
          
        return null;
    }

    /**
     * Validate that the journey has not been dropped off already.
     * 
     * @return \Illuminate\Http\Response|null The response indicating the dropoff status, or null if not dropped off.
     */
    protected function validateDropoff()
    {        
        $dropoffExists = Dropoff::where('journey_id', $this->journey->id)->exists();

        // If a Dropoff record exists, return a 404 Not Found response indicating the group was dropped off
        if ($dropoffExists)  return response()->noContent(Response::HTTP_NOT_FOUND);     

        return null;
    }

    /**
     * Define validation rules for the incoming request data.
     * 
     * @return array An array of validation rules for the 'ID' field.
     */
    protected function validationRules()
    {
        return ['ID' => 'required|integer'];
    }

    /**
     * Set the car property by finding the Car model using the provided ID.
     * 
     * @param int $carId The ID of the car to be set.
     */
    public function setCar($carId)
    {
        $this->car = Car::find($carId);
    }
}
