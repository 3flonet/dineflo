<?php

namespace App\Mail;

use App\Models\SubscriptionInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class SubscriptionInvoiceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $invoice;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(SubscriptionInvoice $invoice)
    {
        $this->invoice = $invoice;
        $this->user = $invoice->subscription->user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice Pembayaran Langganan - ' . ($this->invoice->midtrans_id ?? 'INV-'.$this->invoice->id),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.subscription.invoice',
            with: [
                'planName' => $this->invoice->subscription->plan->name,
                'amount' => number_format($this->invoice->amount, 0, ',', '.'),
                'paidAt' => $this->invoice->paid_at->format('d M Y, H:i'),
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
        $pdf = Pdf::loadView('pdf.subscription-invoice', ['invoice' => $this->invoice]);
        
        return [
            Attachment::fromData(fn () => $pdf->output(), 'invoice-' . ($this->invoice->midtrans_id ?? $this->invoice->id) . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
