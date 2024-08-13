<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Journey;
use DB;
use App\Jobs\AssignCarToJourney;
use Illuminate\Support\Facades\Log;

class JourneyController extends Controller
{
    /**
     * Handle the request to store a new journey.   
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {                
        // Call the verifyContentType method and return if there's an error
        $response = $this->verifyContentType($request);
        if ($response) {
            return $response;
        }
        
        // Retrieve and decode the incoming JSON data from the request
        $data = $request->json()->all();

        // Validate the request data to ensure 'id' and 'people' are valid
        $validator = \Validator::make($data, [
            'id'     => 'required|integer',
            'people' => 'required|integer|min:1|max:6',
        ]);

        // If validation fails, return a 400 Bad Request response with an error message
        if ($validator->fails()) {            
            return response()->json([
                'error' => 'Invalid data format'
            ], Response::HTTP_BAD_REQUEST);
        }

        $id     = $data['id'];        
        
        // Check if a journey with the same ID already exists in the database
        $existingGroup = Journey::find($id);
        if ($existingGroup) {
            // If found, return a 400 Bad Request response indicating the ID is already taken
            return response()->json(['error' => 'Group with this ID already exists.'], Response::HTTP_BAD_REQUEST);
        }     
        
        // Use a transaction to safely store the journey and dispatch the job
        DB::transaction(function () use ($data) {

            // Create a new journey record with the provided data
            $journey = Journey::create($data);            
            
            // Dispatch a job to assign a car to the newly created journey
            AssignCarToJourney::dispatch($journey);

        });       

        // Return a 200 OK response 
        return response()->json(null, Response::HTTP_OK);
    }
}
