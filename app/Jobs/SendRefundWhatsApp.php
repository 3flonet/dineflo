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

class SendRefundWhatsApp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Order $order;
    protected ?string $phone;
    protected float $refundAmount;
    protected string $reason;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order, float $refundAmount, string $reason, ?string $phone = null)
    {
        $this->order        = $order;
        $this->refundAmount = $refundAmount;
        $this->reason       = $reason;
        $this->phone        = $phone ?: $order->customer_phone ?: $order->member?->phone;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (empty($this->phone)) {
            Log::warning("Order #{$this->order->id} cannot send Refund WhatsApp: Phone number missing.");
            return;
        }

        // Clean phone number
        $cleanPhone = preg_replace('/[^0-9]/', '', $this->phone);
        if (str_starts_with($cleanPhone, '08')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        $message = $this->order->generateRefundWhatsAppMessage($this->refundAmount, $this->reason);

        $success = WhatsAppService::sendMessage($this->order->restaurant, $cleanPhone, $message);

        if (!$success) {
            Log::error("Failed to send WhatsApp refund notification for Order #{$this->order->id} to {$cleanPhone}");
        }
    }
}
