<?php

namespace App\Jobs;

use Aws\Sqs\SqsClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ShiftOneLabs\LaravelSqsFifoQueue\Bus\SqsFifoQueueable;

class ExternalApiCallJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SqsFifoQueueable, SerializesModels;

    public $job;

    public string $userEmail;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userEmail)
    {
        $this->userEmail = $userEmail;
//        $this->onConnection('sqs');
//        $this->onQueue('My-test-queue.fifo');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Log::info('user email address', ['email' => $this->userEmail]);
        Log::info('job => ' .' This --- '. json_encode($this->job->payload()));
    }
}
