<?php

namespace App\Services\WhatsApp;

use App\Models\Restaurant;
use App\Services\WhatsApp\Contracts\WhatsAppProvider;
use App\Services\WhatsApp\Providers\FonnteProvider;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Get the appropriate WhatsApp provider for a restaurant.
     *
     * @param Restaurant $restaurant
     * @return WhatsAppProvider|null
     */
    public static function make(Restaurant $restaurant): ?WhatsAppProvider
    {
        if (!$restaurant->wa_is_active || !$restaurant->wa_api_key) {
            return null;
        }

        switch ($restaurant->wa_provider) {
            case 'fonnte':
                return new FonnteProvider($restaurant->wa_api_key);
            
            // Add other providers here as they are implemented
            // case 'wablas':
            //     return new WablasProvider($restaurant->wa_api_key);

            default:
                Log::warning("WhatsApp provider '{$restaurant->wa_provider}' not supported or not set for restaurant ID: {$restaurant->id}");
                return null;
        }
    }

    /**
     * Send message using the restaurant's configured provider.
     *
     * @param Restaurant $restaurant
     * @param string $to
     * @param string $message
     * @return bool
     */
    public static function sendMessage(Restaurant $restaurant, string $to, string $message): bool
    {
        $provider = self::make($restaurant);

        if (!$provider) {
            return false;
        }

        return $provider->send($to, $message);
    }
}
