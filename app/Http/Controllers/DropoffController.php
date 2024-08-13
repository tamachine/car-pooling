<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Journey;
use App\Models\Dropoff;

class DropoffController extends Controller
{
    /**
     * Handle the dropoff request.     
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Retrieve and decode the incoming JSON data from the request
        $data = $request->json()->all();

        // Validate the request data to ensure 'ID' is present and is an integer
        $validator = \Validator::make($data, [
            'id' => 'required|integer',
        ]);

        // If validation fails, return a 400 Bad Request response with an error message
        if ($validator->fails()) {            
            return response()->json([
                'error' => 'Invalid data format'
            ], Response::HTTP_BAD_REQUEST);
        }        

        // Retrieve the journey ID from the validated data
        $journeyId = $data['id'];

        // Attempt to find the Journey record with the provided ID
        $journey = Journey::find($journeyId);

        // If the Journey record is not found, return a 404 Not Found response
        if (!$journey) {
            return response()->json(['error' => 'Group not found'], Response::HTTP_NOT_FOUND);
        }

        // Create a new Dropoff record and associate it with the found Journey
        $dropoff = new Dropoff();
        $dropoff->journey_id = $journeyId;
        $dropoff->save();

        // Return a 204 No Content response indicating the dropoff was successfully recorded
        return response()->noContent();
    }
}
