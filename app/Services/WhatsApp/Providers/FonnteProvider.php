<?php

namespace App\Services\WhatsApp\Providers;

use App\Services\WhatsApp\Contracts\WhatsAppProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteProvider implements WhatsAppProvider
{
    protected string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function send(string $to, string $message): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->timeout(10)->post('https://api.fonnte.com/send', [
                'target' => $to,
                'message' => $message,
                // countryCode otomatis terdeteksi dari target 62xxx
            ]);

            if ($response->successful()) {
                $body = $response->json();
                
                // Cek apakah Fonnte mengembalikan status error
                if (isset($body['status']) && $body['status'] === false) {
                    Log::error('Fonnte API returned error', [
                        'response' => $body,
                        'to' => $to,
                    ]);
                    return false;
                }
                
                return true;
            }

            Log::error('Fonnte API Error: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('Fonnte Provider Exception: ' . $e->getMessage());
            return false;
        }
    }

    public function getName(): string
    {
        return 'fonnte';
    }
}
