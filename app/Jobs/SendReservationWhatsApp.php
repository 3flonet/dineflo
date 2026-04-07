<?php

namespace App\Jobs;

use App\Models\Reservation;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendReservationWhatsApp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Reservation $reservation)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $restaurant = $this->reservation->restaurant;
        $phone = $this->reservation->phone;

        if (!$restaurant->wa_is_active || empty($phone)) {
            return;
        }

        // Clean phone number
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleanPhone, '08')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        $trackingUrl = route('reservations.track', $this->reservation->tracking_hash);
        
        $message = "*Halo {$this->reservation->name}!*\n\n";
        $message .= "Terima kasih telah melakukan reservasi di *{$restaurant->name}*.\n\n";
        $message .= "🗓️ *Tanggal:* " . \Carbon\Carbon::parse($this->reservation->reservation_time)->format('d M Y') . "\n";
        $message .= "⏰ *Waktu:* " . \Carbon\Carbon::parse($this->reservation->reservation_time)->format('H:i') . "\n";
        $message .= "👥 *Tamu:* {$this->reservation->guest_count} Orang\n\n";
        $message .= "Anda dapat memantau status reservasi & nomor meja melalui tautan berikut:\n";
        $message .= $trackingUrl . "\n\n";
        $message .= "Silakan hubungi kami jika ada pertanyaan. Sampai jumpa!";
        
        $success = WhatsAppService::sendMessage($restaurant, $cleanPhone, $message);

        if (!$success) {
            Log::error("Failed to send WhatsApp reservation confirmation for #{$this->reservation->id} to {$cleanPhone}");
        }
    }
}
