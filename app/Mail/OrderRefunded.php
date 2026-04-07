<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderRefunded extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $refundAmount;
    public $reason;
    public $isFullRefund;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, float $refundAmount, string $reason, bool $isFullRefund)
    {
        $this->order        = $order;
        $this->refundAmount = $refundAmount;
        $this->reason       = $reason;
        $this->isFullRefund = $isFullRefund;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Refund Pesanan #' . $this->order->order_number . ' - ' . $this->order->restaurant->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.refunded',
            with: [
                'customerName'  => $this->order->customer_name ?? 'Pelanggan',
                'orderNumber'   => $this->order->order_number,
                'restaurantName'=> $this->order->restaurant->name,
                'refundAmount'  => number_format($this->refundAmount, 0, ',', '.'),
                'totalAmount'   => number_format($this->order->total_amount, 0, ',', '.'),
                'reason'        => $this->reason,
                'isFullRefund'  => $this->isFullRefund,
                'order'         => $this->order,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
