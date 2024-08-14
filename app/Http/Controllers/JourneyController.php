<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Journey;
use DB;
use App\Jobs\AssignCarToJourney;

/**
 * Controller responsible for handling journey-related operations.
 */
class JourneyController extends Controller
{
    /**
     * Handle the request to store a new journey.
     * This method processes the incoming request to create a new journey record.
     *
     * @param \Illuminate\Http\Request $request The incoming request instance.
     * @return \Illuminate\Http\Response The response indicating success or failure.
     */
    public function store(Request $request)
    {                        
        $response = $this->verifyContentType($request);
        if ($response) return $response;

        $this->setData($request->json()->all());
        
        $validator = $this->validateData();
        if($validator) return $validator;       
        
        $this->createJourneyAndDispatchJob();   

        return response()->noContent(Response::HTTP_OK);        
    }

    /**
     * Create a new journey record and dispatch a job to assign a car to the journey.
     * This method handles the database transaction to ensure both operations succeed or fail together.
     * 
     * @return void
     */
    protected function createJourneyAndDispatchJob()
    {        
        DB::transaction(function () {            
            
            $journey = Journey::create($this->data);            
                        
            AssignCarToJourney::dispatch($journey); // Dispatch a job to assign a car to the newly created journey

        });   
    }

    /**
     * Override validateData method to include additional checks.
     * This method performs additional validation to ensure the journey does not already exist.
     * 
     * @return \Illuminate\Http\Response|null A JSON response with validation errors if validation fails, otherwise null.
     */
    protected function validateData()
    {        
        $validator = parent::validateData();        
        if($validator) return $validator;

        $check = $this->journeyAlreadyExists();
        if($check) return $check;

        return null;
    }  

    /**
     * Check if a journey with the same ID already exists.
     * This method verifies if a journey with the provided ID is already present in the database.
     * 
     * @return \Illuminate\Http\Response|null A JSON response with an error message if the journey already exists, otherwise null.
     */
    protected function journeyAlreadyExists()
    {
        $existingGroup = Journey::find($this->data['id']);

        if ($existingGroup) return response()->json(['error' => 'Group with this ID already exists.'], Response::HTTP_BAD_REQUEST);

        return null;
    }

    /**
     * Define the validation rules for the journey data.
     * The 'id' field must be present and be an integer. The 'people' field must be an integer between 1 and 6.
     *
     * @return array The validation rules for the journey data.
     */
    protected function validationRules()
    {
        return [
            'id'     => 'required|integer',
            'people' => 'required|integer|min:1|max:6'
        ];
    }
}
