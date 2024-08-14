<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class Controller
{

    const CONTENT_TYPE_APPLICATION_JSON = 'application/json';
    CONST CONTENT_TYPE_APPLICATION_FORM = 'application/x-www-form-urlencoded';

     /**
     * Verify that the Content-Type header is application/json.
     *
     * @param \Illuminate\Http\Request $request
     * @param String $contentType
     * @return \Illuminate\Http\Response|null
     */
    protected function verifyContentType(Request $request, String $contentType = self::CONTENT_TYPE_APPLICATION_JSON)
    {
        // Verify content-type
        if ($request->header('Content-Type') !== $contentType) {
            return response()->json([
                'error' => 'Invalid content type'
            ], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        return null; // No error, proceed with the request
    }

    
}
