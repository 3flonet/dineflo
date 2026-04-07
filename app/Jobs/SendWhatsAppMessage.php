<?php

namespace App\Jobs;

use App\Models\Restaurant;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     * @var int
     */
    public $backoff = 30;

    /**
     * The number of seconds the job can run before timing out.
     * @var int
     */
    public $timeout = 60;


    /**
     * Create a new job instance.
     */
    public function __construct(
        public Restaurant $restaurant,
        public string $to,
        public string $message,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            WhatsAppService::sendMessage($this->restaurant, $this->to, $this->message);
        } catch (\Exception $e) {
            Log::error('SendWhatsAppMessage Job Failed: ' . $e->getMessage(), [
                'restaurant_id' => $this->restaurant->id,
                'to' => $this->to,
            ]);
            
            // Fail the job so it can be retried if configured
            throw $e;
        }
    }
}
