<?php

namespace App\Services\WhatsApp\Contracts;

interface WhatsAppProvider
{
    /**
     * Send a WhatsApp message.
     *
     * @param string $to The phone number (e.g., 628123456789)
     * @param string $message The message content
     * @return bool
     */
    public function send(string $to, string $message): bool;

    /**
     * Get the name of the provider.
     *
     * @return string
     */
    public function getName(): string;
}
