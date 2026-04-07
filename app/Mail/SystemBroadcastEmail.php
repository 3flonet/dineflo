<?php

namespace App\Mail;

use App\Models\SystemBroadcast;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SystemBroadcastEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SystemBroadcast $systemBroadcast, public User $owner)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->systemBroadcast->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.system.broadcast',
            with: [
                'content' => $this->systemBroadcast->content,
                'owner' => $this->owner,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
