<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Response;

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
