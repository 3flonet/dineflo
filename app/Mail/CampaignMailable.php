<?php

namespace App\Mail;

use App\Models\EmailCampaign;
use App\Models\Restaurant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class CampaignMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectLine;
    public $content;
    public $restaurant;
    public $campaign;

    /**
     * Create a new message instance.
     */
    public function __construct($subjectLine, $content, Restaurant $restaurant, EmailCampaign $campaign)
    {
        $this->subjectLine = $subjectLine;
        $this->content = $content;
        $this->restaurant = $restaurant;
        $this->campaign = $campaign;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.campaign',
            with: [
                'content' => $this->content,
                'restaurant' => $this->restaurant,
                'showBranding' => !$this->restaurant->owner->hasFeature('Remove Branding'),
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
