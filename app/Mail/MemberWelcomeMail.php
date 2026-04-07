<?php

namespace App\Mail;

use App\Models\Member;
use App\Models\Restaurant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MemberWelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Member $member,
        public Restaurant $restaurant,
        public string $portalUrl,
    ) {
        $this->resolveMailer($restaurant);
    }

    /**
     * Resolve SMTP config for the restaurant.
     */
    protected function resolveMailer(Restaurant $restaurant): void
    {
        if (!empty($restaurant->email_marketing_smtp_host)) {
            config([
                'mail.mailers.restaurant_smtp' => [
                    'transport'  => 'smtp',
                    'host'       => $restaurant->email_marketing_smtp_host,
                    'port'       => $restaurant->email_marketing_smtp_port ?? 587,
                    'encryption' => $restaurant->email_marketing_smtp_encryption ?? 'tls',
                    'username'   => $restaurant->email_marketing_smtp_username,
                    'password'   => $restaurant->email_marketing_smtp_password,
                ],
                'mail.from.address' => $restaurant->email_marketing_smtp_username,
                'mail.from.name'    => $restaurant->name,
            ]);
            $this->mailer = 'restaurant_smtp';
        } else {
            config(['mail.from.name' => $restaurant->name]);
            $this->mailer = config('mail.default');
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Selamat Datang di {$this->restaurant->name}! 🎉",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.member.welcome');
    }

    public function attachments(): array
    {
        return [];
    }
}
