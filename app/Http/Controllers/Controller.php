<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class Controller
{
     /**
     * Verify that the Content-Type header is application/json.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|null
     */
    protected function verifyContentType(Request $request)
    {
        // Verify content-type
        if ($request->header('Content-Type') !== 'application/json') {
            return response()->json([
                'error' => 'Invalid content type'
            ], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        return null; // No error, proceed with the request
    }
}
