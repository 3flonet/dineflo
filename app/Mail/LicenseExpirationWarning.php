<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class LicenseExpirationWarning extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $customerName,
        public string $customerEmail,
        public string $licenseKey,
        public ?string $expiresAt,
        public ?string $gracePeriodUntil,
        public string $daysRemaining,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Peringatan: Lisensi ' . config('app.name', 'Dineflo') . ' Anda Akan Kadaluarsa',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.license-expiration-warning',
            with: [
                'customerName' => $this->customerName,
                'licenseKey' => $this->licenseKey,
                'expiresAt' => $this->expiresAt,
                'gracePeriodUntil' => $this->gracePeriodUntil,
                'daysRemaining' => $this->daysRemaining,
                'supportUrl' => 'https://wa.me/628123456789',
                'renewUrl' => 'https://dineflo.test',
            ],
        );
    }
}
