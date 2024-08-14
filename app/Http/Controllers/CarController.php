<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;
use App\Models\Car;
use App\Models\Journey;
use App\Models\Dropoff;

class CarController extends Controller
{
    /**
     * Updates the list of available cars and resets the application state.    
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {                 
        
        // Call the verifyContentType method and return if there's an error
        $response = $this->verifyContentType($request);
        if ($response) {
            return $response;
        }

        $data = $request->json()->all();
        
        // Check if the data is empty or not an array
        if (empty($data) || !is_array($data)) {
            return response()->json([
                'error' => 'Invalid data format: The data should be a non-empty array'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Validate the incoming request data
        $validator = \Validator::make($data, [
            '*.id'      => 'required|integer|distinct',
            '*.seats'   => 'required|integer|min:1',
        ]);

        // If validation fails, return a 400 Bad Request response with error details
        if ($validator->fails()) {            
            return response()->json([
                'error' => 'Invalid data: '. $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }       

        // Reset the application state by deleting all related records
        $this->resetApplicationState();
        
        // Store the new list of cars in the database
        foreach ($data as $item) {
            Car::create($item);            
        }
        
        // Return a 200 OK response 
        return response()->noContent(Response::HTTP_OK); 
    }

    /**
     * Resets the application state by deleting all records from related tables.
     *    
     * @return void
     */
    protected function resetApplicationState()
    {
        DB::beginTransaction();

        try {
            // Delete all records from the Dropoff, Journey, and Car tables
            Dropoff::truncate();
            Journey::truncate();            
            Car::truncate();
            
            // Commit the transaction if all deletions are successful
            DB::commit();
        } catch (\Exception $e) {
            // Roll back the transaction if an error occurs during deletion
            DB::rollBack();
                        
            throw $e;
        }
    }
}
