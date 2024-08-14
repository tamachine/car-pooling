<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Journey;
use App\Models\Car;
use App\Models\Dropoff;

class LocateController extends Controller
{
    /**
     * Handle the locate request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function locate(Request $request)
    {              
        // Call the verifyContentType method and return if there's an error
        $response = $this->verifyContentType($request, self::CONTENT_TYPE_APPLICATION_FORM);
        if ($response) {
            return $response;
        }
         
        // Retrieve and decode the incoming JSON data from the request
        $data = $request->all();

        // Validate the request data to ensure 'ID' is present and is an integer
        $validator = \Validator::make($data, [
           'ID' => 'required|integer',
        ]);

        // If validation fails, return a 400 Bad Request response with an error message
        if ($validator->fails()) return response()->noContent(Response::HTTP_BAD_REQUEST); 

        // Retrieve the journey ID from the validated data
        $journeyId = $data['ID'];

        // Attempt to find the Journey record with the provided ID
        $journey = Journey::find($journeyId);

        // If the Journey record is not found, return a 404 Not Found response
        if (!$journey) return response()->noContent(Response::HTTP_NOT_FOUND);     
       
        // Check if the journey has been marked as dropped off by looking for a related Dropoff record
        $dropoffExists = Dropoff::where('journey_id', $journeyId)->exists();

        // If a Dropoff record exists, return a 404 Not Found response indicating the group was dropped off
        if ($dropoffExists)  return response()->noContent(Response::HTTP_NOT_FOUND);  

        // Check if the journey has an associated car by looking at the car_id field
        if ($journey->car_id) {
            // Attempt to find the Car record with the associated car ID
            $car = Car::find($journey->car_id);

            // If the Car is found, return a 200 OK response with the car details (ID and seats)
            if ($car) {
                return response()->json([
                   'id' => $car->id,
                   'seats' => $car->seats,
                ], Response::HTTP_OK);
            } 
        } 

        // If the journey has no car assigned or no car was found, return a 204
        return response()->noContent();
    }
}
