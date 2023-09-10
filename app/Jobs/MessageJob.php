<?php

namespace App\Jobs;

use App\Events\PrivateSendMessage;
use App\Models\Chat;
use Exception;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class MessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // public $tries = 1;
    public $dataMessage;
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->dataMessage = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("data message");
        Log::info(json_encode($this->dataMessage));
        try {
            Chat::create($this->dataMessage);
            broadcast(new PrivateSendMessage($this->dataMessage));
        } catch (\Throwable $e) {
            // Queue::connection('rabbitmq')->pushRaw($e->getMessage());
            $this->fail();
            Log::info(json_encode($e->getMessage()));
        }
    }

    public function failed(): void {
        Log::info('failed');
        MessageJob::dispatch(Log::info("arifin fail"))->onQueue('message_fail');
    }

    public function retryUntil(): DateTime
    {
        return now()->addMinutes(10);
    }
}
