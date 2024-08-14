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
        // Return a 200 OK response 
        return response()->noContent(Response::HTTP_OK); 
    }
}
