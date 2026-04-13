<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Order;
use App\Models\Restaurant;

class MidtransService
{
    /**
     * @param Restaurant|null $restaurant
     *   Jika diberikan dan memiliki akun Midtrans sendiri (gateway_mode = 'own'),
     *   maka key restoran akan dipakai. Jika tidak, fallback ke .env Dineflo.
     */
    public function __construct(?Restaurant $restaurant = null)
    {
        $settings = app(\App\Settings\GeneralSettings::class);

        // Cek apakah restoran punya key Midtrans sendiri
        $useOwn = $restaurant
            && $restaurant->gateway_mode === 'own'
            && !empty(trim($restaurant->midtrans_server_key));

        if ($useOwn) {
            $sKey = $restaurant->midtrans_server_key;
            try { $sKey = \Illuminate\Support\Facades\Crypt::decryptString($sKey); } catch (\Exception $e) {}
            Config::$serverKey    = trim($sKey);
            Config::$isProduction = !str_starts_with(Config::$serverKey, 'SB-');
        } else {
            // Gunakan pengaturan dari GeneralSettings (Admin Panel)
            $sKey = $settings->midtrans_server_key;
            try { $sKey = \Illuminate\Support\Facades\Crypt::decryptString($sKey); } catch (\Exception $e) {}
            
            Config::$serverKey    = !empty(trim($sKey)) ? trim($sKey) : config('midtrans.server_key');
            
            // Auto-detect isProduction dari Server Key untuk keamanan extra
            Config::$isProduction = !str_starts_with(trim(Config::$serverKey), 'SB-');
        }

        Config::$isSanitized  = config('midtrans.is_sanitized', true);
        Config::$is3ds        = config('midtrans.is_3ds', true);
    }

    public function isUsingPlaceholderKey()
    {
        return trim(Config::$serverKey) === 'SB-Mid-server' || empty(Config::$serverKey);
    }


    public function createSnapToken(Order $order, array $paramsOverride = [])
    {
        // Ensure accurate gross amount
        $grossAmount = (int) $order->total_amount;

        // Generate Unique Order ID for Midtrans
        // Format: ORD-{ORDER_ID}-{TIMESTAMP}
        $midtransOrderId = 'ORD-' . $order->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'phone' => $order->customer_phone,
            ],
            // Optional: Send item details for clearer invoice in Midtrans Dashboard
            /*
            'item_details' => $order->items->map(function ($item) {
                return [
                    'id' => $item->menu_item_id,
                    'price' => (int) $item->unit_price,
                    'quantity' => $item->quantity,
                    'name' => substr($item->menuItem->name, 0, 50),
                ];
            })->toArray(),
            */
            'callbacks' => [
                'finish' => route('order.summary', $order->id),
                'error' => route('order.summary', $order->id),
                'pending' => route('order.summary', $order->id),
            ],
        ];
        
        // Merge with overrides (e.g. enabled_payments)
        if (!empty($paramsOverride)) {
            $params = array_merge($params, $paramsOverride);
        }

        try {
            if (app()->environment('local') && $this->isUsingPlaceholderKey()) {
                return 'MOCK_TOKEN_' . time();
            }
            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (\Exception $e) {
            \Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return null;
        }
    }

    public function createSubscriptionSnapToken(\App\Models\Subscription $subscription)
    {
        // Get plan info
        $plan     = $subscription->plan;
        $isYearly = ($subscription->billing_period === 'yearly');
        
        // Total price from plan (Cast to int for Midtrans)
        $grossAmount = $isYearly ? (int) $plan->yearly_price : (int) $plan->price;
        $labelPeriod = $isYearly ? '1 year' : $plan->duration_days . ' days';
        
        // Generate Invoice ID (Unique for every retry to avoid duplicate order_id error)
        $midtransOrderId = 'SUB-' . $subscription->id . '-' . time();
        
        $params = [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $subscription->user->name,
                'email' => $subscription->user->email,
            ],
            'item_details' => [
                [
                    'id' => 'PLAN-' . $plan->id . ($isYearly ? '-Y' : '-M'),
                    'price' => $grossAmount,
                    'quantity' => 1,
                    'name' => substr("Subscription: " . $plan->name . " (" . $labelPeriod . ")", 0, 50),
                ]
            ],
            'callbacks' => [
                'finish' => route('filament.restaurant.pages.my-subscription', [
                    'tenant' => $subscription->user->restaurant_id ?? $subscription->user->ownedRestaurants()->first()?->id
                ]),
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            
            // Update subscription with latest attempt info
            $subscription->midtrans_id = $midtransOrderId;
            $subscription->save();

            return $snapToken;
        } catch (\Exception $e) {
            \Log::error('Midtrans Subscription Snap Error: ' . $e->getMessage());
            return null;
        }
    }
}
