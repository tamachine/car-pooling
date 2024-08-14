<?php

namespace App\Jobs;

use App\Models\Journey;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Services\CarPooling;
use Illuminate\Support\Facades\Log;
use DateTime;

/**
 * Job to assign a car to a journey.
 *
 * This job is responsible for assigning an available car to a journey.
 * It processes the assignment asynchronously by placing the job on a queue,
 * ensuring that the operation is handled in the background without blocking
 * the main application. The job utilizes a transaction to maintain data integrity,
 * and it includes logic to retry the assignment if it fails initially.
 * Errors during the assignment are logged, and the job is re-queued for another
 * attempt if necessary.
 */
class AssignCarToJourney implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // The Journey instance that this job will process
    protected $journey;
    
    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil(): DateTime
    {
        // Set the job to retry until 20 minutes from now
        return now()->addMinutes(20);
    }

    /**
     * Create a new job instance.
     *
     * This constructor initializes the job with the Journey instance that needs
     * a car assigned to it.
     *
     * @param \App\Models\Journey $journey
     * @return void
     */
    public function __construct(Journey $journey)
    {
        $this->journey = $journey;        
    }

    /**
     * Execute the job.
     *
     * This method handles the logic to assign a car to the journey. It uses a transaction
     * to ensure data consistency and retries the job if it fails to assign a car. In case
     * of an exception, the transaction is rolled back, and the job is re-queued for retry.
     *
     * @param \App\Services\CarPooling $carPooling
     * @return void
     */
    public function handle(CarPooling $carPooling) 
    {                   
                
        DB::beginTransaction(); // Begin a transaction to ensure data consistency

        try {
            
            // Attempt to assign a car to the journey using the CarPooling service
            if (!$carPooling->pool($this->journey)) {
                // If no car was assigned, release the job back to the queue with a delay of 10 seconds
                $this->release(10);
            }   
                        
            DB::commit(); // Commit the transaction if car assignment was successful

        } catch (\Exception $e) {
            
            DB::rollBack(); // Roll back the transaction in case of an error
            
            // Log the error for debugging purposes
            Log::error("Error assigning car to journey: " . $e->getMessage());
            
            // Release the job back to the queue with a delay of 10 seconds to retry
            $this->release(10);
        }                    
                     
    }
}
