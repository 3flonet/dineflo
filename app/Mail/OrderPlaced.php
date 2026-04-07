<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderPlaced extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Pesanan #' . $this->order->order_number . ' - ' . $this->order->restaurant->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.placed',
            with: [
                'customerName' => $this->order->customer_name,
                'orderNumber' => $this->order->order_number,
                'restaurantName' => $this->order->restaurant->name,
                'totalAmount' => number_format($this->order->total_amount, 0, ',', '.'),
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
        // Only attach if paid to provide the final invoice
        if ($this->order->payment_status === 'paid') {
            $this->order->load(['items.menuItem', 'items.variant', 'restaurant', 'table']);
            
            $pdf = Pdf::loadView('pdf.invoice', ['order' => $this->order, 'payment' => null]);
            
            return [
                Attachment::fromData(fn () => $pdf->output(), 'invoice-' . $this->order->order_number . '.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
