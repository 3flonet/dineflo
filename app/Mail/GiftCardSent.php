<?php

namespace App\Mail;

use App\Models\GiftCard;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GiftCardSent extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public GiftCard $giftCard)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎁 Kamu Mendapatkan Gift Card dari ' . $this->giftCard->restaurant->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.gift-cards.sent',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
