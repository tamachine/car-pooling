<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class StatusController
{
    /**
     * Handle the incoming request to check the status of the service.
     * 
     * This method responds with a 200 OK status, indicating that the service is up and running.
     *
     * @return \Illuminate\Http\Response The response indicating the service status.
     */
    public function index()
    {                                 
        return response()->noContent(Response::HTTP_OK); 
    }
}
