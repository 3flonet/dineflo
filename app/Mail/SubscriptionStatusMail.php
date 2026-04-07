<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subscription;
    public $type; // 'warning' or 'expired'

    /**
     * Create a new message instance.
     */
    public function __construct($user, $subscription, $type = 'warning')
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->type = $type;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $siteName = config('app.name', 'Dineflo');
        $subject = $this->type === 'expired' 
            ? "Your Subscription has Expired - {$siteName}" 
            : "Subscription Expiry Notice - {$siteName}";

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $tenant = $this->user->ownedRestaurants()->first();
        $url = $tenant 
            ? route('filament.restaurant.pages.my-subscription', ['tenant' => $tenant->slug])
            : route('filament.restaurant.auth.login');

        return new Content(
            markdown: 'emails.subscription.status',
            with: [
                'daysLeft' => (int) now()->diffInDays($this->subscription->expires_at, false),
                'url' => $url,
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
