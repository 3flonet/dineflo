<?php

namespace App\Jobs;

use App\Models\Restaurant;
use App\Services\MarketingMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhitelabelMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $restaurant;
    protected $recipient;
    protected $mailable;

    /**
     * Create a new job instance.
     */
    public function __construct(Restaurant $restaurant, string $recipient, $mailable)
    {
        $this->restaurant = $restaurant;
        $this->recipient = $recipient;
        $this->mailable = $mailable;
    }

    /**
     * Execute the job.
     */
    public function handle(MarketingMailService $mailService): void
    {
        $mailService->sendTransactionalEmail($this->restaurant, $this->recipient, $this->mailable);
    }
}
