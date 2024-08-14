<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;
use App\Models\Journey;

/**
 * Abstract base controller class that provides common functionality for other controllers.
 * This class handles common tasks such as content type verification, data validation, and
 * basic operations on the Journey model.
 */
abstract class Controller
{
    const CONTENT_TYPE_APPLICATION_JSON = 'application/json';
    CONST CONTENT_TYPE_APPLICATION_FORM = 'application/x-www-form-urlencoded';
    
    protected $data;
    protected $journey;

    /**
     * Set the data property.
     * 
     * @param array $data The data to be set.
     */
    protected function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Set the journey property by finding the Journey model using the provided ID.
     * 
     * @param int $journeyId The ID of the journey to be set.
     */
    public function setJourney($journeyId)
    {
        $this->journey = Journey::find($journeyId);
    }

     /**
     * Verify that the Content-Type header matches the expected content type.
     *
     * @param \Illuminate\Http\Request $request The incoming request instance.
     * @param string $contentType The expected content type. Defaults to 'application/json'.
     * @return \Illuminate\Http\Response|null A JSON response with an error message if the content type is invalid, otherwise null.
     */
    protected function verifyContentType(Request $request, String $contentType = self::CONTENT_TYPE_APPLICATION_JSON)
    {
        if ($request->header('Content-Type') !== $contentType) {
            return response()->json([
                'error' => 'Invalid content type'
            ], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        return null; // No error, proceed with the request
    }

    /**
     * Validate the data stored in the $data property using the validation rules defined in child classes.
     *
     * @return \Illuminate\Http\Response|null A JSON response with validation errors if validation fails, otherwise null.
     */
    protected function validateData()
    {
        // Validate the incoming request data
        $validator = Validator::make($this->data, $this->validationRules());

        // If validation fails, return a 400 Bad Request response with error details
        if ($validator->fails()) return response()->json(['error' =>  'Invalid data: s'. $validator->errors()], Response::HTTP_BAD_REQUEST);

        return null; // No error, proceed with the request         
    }    

    /**
     * Abstract method that must be implemented by child classes to define the validation rules
     * for the data being processed.
     *
     * @return array An array of validation rules.
     */
    abstract protected function validationRules();        
}
