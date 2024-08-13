<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Models\Journey;
use App\Models\Car;
use App\Models\Dropoff;

class StatusController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                         
        // Return a JSON response with a status message and HTTP 200 OK status
        return response()->json(['status' => 'Service is up and running'], Response::HTTP_OK);
    }
}
