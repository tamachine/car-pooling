<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;
use App\Models\Car;
use App\Models\Journey;
use App\Models\Dropoff;

/**
 * Controller responsible for handling car-related operations.
 */
class CarController extends Controller
{
    /**
     * Updates the list of available cars and resets the application state.
     * This method processes the request data, validates it, and performs 
     * state reset and car storage operations.
     *
     * @param \Illuminate\Http\Request $request The incoming request instance.
     * @return \Illuminate\Http\Response The response indicating success or failure.
     */
    public function update(Request $request)
    {                         
        $response = $this->verifyContentType($request);
        if ($response) return $response;        
        
        $this->setData($request->json()->all());        
                
        $validator = $this->validateData();
        if($validator) return $validator;               
        
        $this->resetApplicationState();    
        
        $this->storeCars();
        
        return response()->noContent(Response::HTTP_OK); 
    }

    /**
     * Resets the application state by deleting all records from related tables.
     * This method wraps the deletion operations in a database transaction 
     * to ensure that either all deletions succeed or none at all.
     *
     * @return void
     */
    protected function resetApplicationState()
    {
        DB::beginTransaction();

        try {            
            
            $this->truncateTables();
                        
            DB::commit(); // Commit the transaction if all deletions are successful

        } catch (\Exception $e) {
            
            DB::rollBack(); // Roll back the transaction if an error occurs during deletion
                        
            throw $e;
        }
    }

    /**
     * Override validateData method to include additional checks.
     * Validate the data by first checking if it is empty or not an array,
     * and then applying the validation rules defined in the parent class.
     *
     * @return \Illuminate\Http\Response|null A JSON response with validation errors if validation fails, otherwise null.
     */
    protected function validateData()
    {
        $check = $this->checkEmptyOrNotArray();
        if($check) return $check;

        $validator = parent::validateData();        
        if($validator) return $validator;

        return null;
    }   

    /**
     * Check if the data is either empty or not an array.
     *
     * @return \Illuminate\Http\Response|null A JSON response with an error message if the data is invalid, otherwise null.
     */
    protected function checkEmptyOrNotArray()
    {        
        if (empty($this->data) || !is_array($this->data)) {
            return response()->json([
                'error' => 'Invalid data format: The data should be a non-empty array'
            ], Response::HTTP_BAD_REQUEST);
        }

        return null;
    }

    /**
     * Store the new list of cars in the database.
     * This method iterates over the provided data and creates Car records for each item.
     *
     * @return void
     */
    protected function storeCars()
    {        
        foreach ($this->data as $item) {
            Car::create($item);            
        }
    }

    /**
     * Define the validation rules for the car data.
     * Each item in the data array should have a unique ID and a valid number of seats.
     *
     * @return array The validation rules for the car data.
     */
    protected function validationRules()
    {
        return [
            '*.id'      => 'required|integer|distinct',
            '*.seats'   => 'required|integer|min:1',
        ];
    }

    /**
     * Truncate the records from the Dropoff, Journey, and Car tables.
     * This method deletes all records from the specified tables.
     *
     * @return void
     */
    protected function truncateTables()
    {
        Dropoff::truncate();
        Journey::truncate();            
        Car::truncate();
    }
}
