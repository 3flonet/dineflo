<?php

namespace App\Jobs;

use App\Models\SystemBroadcast;
use App\Models\User;
use App\Models\SystemBroadcastLog;
use App\Settings\GeneralSettings;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSystemBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public SystemBroadcast $broadcast,
        public User $user,
        public bool $waActive,
        public string $waProvider,
        public $waSettingsData // Passed as array or object to avoid repeated DB lookups
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $channel = $this->broadcast->channel;
        $emailSent = true;
        $waSent = true;

        try {
            // ── Send Email ──
            if (in_array($channel, ['email', 'both']) && $this->user->email) {
                Mail::to($this->user->email)->send(
                    new \App\Mail\SystemBroadcastEmail($this->broadcast, $this->user)
                );
            }

            // ── Send WhatsApp ──
            if (in_array($channel, ['whatsapp', 'both']) && $this->waActive && $this->broadcast->wa_message && $this->user->phone) {
                $cleanPhone = $this->normalizePhone($this->user->phone);
                if ($cleanPhone) {
                    $waSent = $this->sendWhatsApp($this->waProvider, $this->waSettingsData, $cleanPhone, $this->broadcast->wa_message);
                }
            }

            $overallSuccess = $emailSent && $waSent;

            SystemBroadcastLog::updateOrCreate(
                ['system_broadcast_id' => $this->broadcast->id, 'user_id' => $this->user->id],
                ['status' => $overallSuccess ? 'sent' : 'failed', 'sent_at' => now()]
            );

        } catch (\Exception $e) {
            Log::error("Broadcast Job Failed for User #{$this->user->id}: " . $e->getMessage());
            
            SystemBroadcastLog::updateOrCreate(
                ['system_broadcast_id' => $this->broadcast->id, 'user_id' => $this->user->id],
                ['status' => 'failed', 'error_message' => $e->getMessage()]
            );
            
            throw $e;
        }
    }

    protected function sendWhatsApp(string $provider, $settings, string $phone, string $message): bool
    {
        // Re-using logic from original ProcessSystemBroadcast
        // Note: $settings here is the data array passed from handle
        try {
            return match ($provider) {
                'fonnte' => (function () use ($settings, $phone, $message) {
                    $key = $settings['fonnte_key'] ?? null;
                    if (!$key) return false;
                    $res = Http::timeout(10)->withHeaders(['Authorization' => $key])->post('https://api.fonnte.com/send', ['target' => $phone, 'message' => $message]);
                    return $res->successful();
                })(),
                'watzap' => (function () use ($settings, $phone, $message) {
                    $key = $settings['watzap_key'] ?? null;
                    $num = $settings['watzap_number'] ?? null;
                    if (!$key || !$num) return false;
                    $res = Http::timeout(15)->post('https://api.watzap.id/v1/send_message', ['api_key' => $key, 'number_key' => $num, 'phone_no' => $phone, 'message' => $message]);
                    return $res->successful();
                })(),
                'watsap' => (function () use ($settings, $phone, $message) {
                    $key = $settings['watsap_key'] ?? null;
                    $dev = $settings['watsap_device'] ?? null;
                    if (!$key || !$dev) return false;
                    $res = Http::timeout(15)->post('https://api.watsap.id/send-message', ['api-key' => $key, 'id_device' => $dev, 'no_hp' => $phone, 'pesan' => $message]);
                    return $res->successful();
                })(),
                default => false,
            };
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function normalizePhone(?string $phone): ?string
    {
        if (empty($phone)) return null;
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($clean, '08')) $clean = '62' . substr($clean, 1);
        elseif (str_starts_with($clean, '8') && strlen($clean) >= 9) $clean = '62' . $clean;
        return (strlen($clean) < 10 || !str_starts_with($clean, '62')) ? null : $clean;
    }
}
