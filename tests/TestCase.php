<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\Response;

abstract class TestCase extends BaseTestCase
{    
    public function assertInvalidContentTypeRejected()
    {        
        // Send request with invalid content type
        $response = $this->json('PUT', '/cars', [], ['Content-Type' => 'text/plain']);

        // Assert that the response is 415 
        $response->assertStatus(Response::HTTP_UNSUPPORTED_MEDIA_TYPE);        
    }
}
