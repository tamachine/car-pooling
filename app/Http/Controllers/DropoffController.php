<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Dropoff;

/**
 * Controller responsible for handling dropoff-related operations.
 */
class DropoffController extends Controller
{

    protected $journey;

    /**
     * Handle the dropoff request.
     * This method processes the incoming request to record a dropoff for a journey.
     *
     * @param \Illuminate\Http\Request $request The incoming request instance.
     * @return \Illuminate\Http\Response The response indicating success or failure.
     */
    public function store(Request $request)
    {
        $response = $this->verifyContentType($request, self::CONTENT_TYPE_APPLICATION_FORM);
        if ($response) return $response;
                 
        $this->setData($request->all());

        $validator = $this->validateData();
        if($validator) return $validator;   
        
        $this->setJourney($this->data['ID']);

        $validateJourney = $this->validateJourney();
        if($validateJourney) return $validateJourney;           

        $this->createDropoff();
        
        return response()->noContent();
    }

    /**
     * Validate that the journey exists.
     * This method checks if the journey instance is set and returns a 404 Not Found response if not.
     *
     * @return \Illuminate\Http\Response|null A JSON response with an error message if the journey is not found, otherwise null.
     */
    protected function validateJourney()
    {
        if (!$this->journey) return response()->json(['error' => 'Group not found'], Response::HTTP_NOT_FOUND);

        return null;
    }

    /**
     * Define the validation rules for the dropoff data.
     * The 'ID' field must be present and be an integer.
     *
     * @return array The validation rules for the dropoff data.
     */
    protected function validationRules()
    {
        return [ 'ID' => 'required|integer'];
    }

     /**
     * Create a new Dropoff record and associate it with the found Journey.
     * This method creates a new Dropoff instance, sets the journey ID, and saves it to the database.
     *
     * @return void
     */
    protected function createDropoff()
    {        
        $dropoff = new Dropoff();
        $dropoff->journey_id = $this->journey->id;
        $dropoff->save();
    }

}
