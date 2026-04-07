<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeedbackRewardMail extends Mailable
{
    use Queueable, SerializesModels;

    public $restaurant;
    public $rewardType;
    public $rewardValue;
    public $customerName;

    /**
     * Create a new message instance.
     */
    public function __construct($restaurant, $rewardType, $rewardValue, $customerName)
    {
        $this->restaurant = $restaurant;
        $this->rewardType = $rewardType;
        $this->rewardValue = $rewardValue;
        $this->customerName = $customerName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Hadiah Spesial untuk Anda dari ' . $this->restaurant->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.feedback-reward',
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
