<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOrderWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Order $order;
    protected ?string $phone;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order, ?string $phone = null)
    {
        $this->order = $order;
        $this->phone = $phone ?: $order->customer_phone;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (empty($this->phone)) {
            Log::warning("Order #{$this->order->id} cannot send WhatsApp: Phone number missing.");
            return;
        }

        // Clean phone number (remove +, spaces, etc)
        $cleanPhone = preg_replace('/[^0-9]/', '', $this->phone);
        
        // Ensure starting with 62 for Indonesia if needed
        if (str_starts_with($cleanPhone, '08')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        $message = $this->order->generateWhatsAppMessage();
        
        $success = WhatsAppService::sendMessage($this->order->restaurant, $cleanPhone, $message);

        if (!$success) {
            Log::error("Failed to send WhatsApp receipt for Order #{$this->order->id} to {$cleanPhone}");
        }
    }
}
