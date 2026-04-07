<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\RestaurantBalanceLedger;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function __construct()
    {
        $settings = app(\App\Settings\GeneralSettings::class);

        // Default: pakai key Dineflo dari GeneralSettings (Admin Panel) atau fallback ke .env
        Config::$serverKey    = !empty(trim($settings->midtrans_server_key ?? '')) ? trim($settings->midtrans_server_key) : config('midtrans.server_key');
        Config::$isProduction = $settings->midtrans_is_production ?? config('midtrans.is_production');
        
        Config::$isSanitized  = config('midtrans.is_sanitized', true);
        Config::$is3ds        = config('midtrans.is_3ds', true);
    }

    // ── Biaya Midtrans per payment type ─────────────────────────────────────
    // Dibaca dari GeneralSettings (dapat diubah Super Admin) dengan fallback default.
    protected function calculateFee(string $paymentType, float $grossAmount): array
    {
        $settings = app(\App\Settings\GeneralSettings::class);

        $qrisTypes = ['qris', 'gopay', 'shopeepay', 'other_qris'];

        if (in_array($paymentType, $qrisTypes)) {
            $percentage = $settings->midtrans_qris_fee_percentage ?? 0.70;
            $feeAmount  = round($grossAmount * $percentage / 100);
            return ['percentage' => $percentage, 'fee_amount' => $feeAmount];
        }

        if ($paymentType === 'bank_transfer') {
            $flat = $settings->midtrans_va_fee_flat ?? 4000;
            return ['percentage' => 0, 'fee_amount' => $flat];
        }

        if ($paymentType === 'credit_card') {
            $percentage = $settings->midtrans_cc_fee_percentage ?? 2.00;
            $feeAmount  = round($grossAmount * $percentage / 100);
            return ['percentage' => $percentage, 'fee_amount' => $feeAmount];
        }

        if ($paymentType === 'cstore') {
            $flat = $settings->midtrans_cstore_fee_flat ?? 5000;
            return ['percentage' => 0, 'fee_amount' => $flat];
        }

        return ['percentage' => 0, 'fee_amount' => 0];
    }

    public function notificationHandler(Request $request)
    {
        try {
            $payload = $request->json()->all();
            $rawOrderId = $payload['order_id'] ?? null;
            
            // Ekstrak ID order secepatnya untuk cek konfigurasi khusus restoran
            if ($rawOrderId && !str_starts_with($rawOrderId, 'SUB-')) {
                $parts = explode('-', $rawOrderId);
                if (count($parts) >= 2 && $parts[1] === 'SPLIT') {
                    $orderId = $parts[0];
                } else {
                    $orderId = (count($parts) >= 3 && $parts[0] === 'ORD') ? $parts[1] : $rawOrderId;
                }
                
                $order = Order::with('restaurant')->find($orderId);
                // Override the global Config so the default SDK Notification class can properly verify the signature later (if needed)
                if ($order && $order->restaurant && $order->restaurant->gateway_mode === 'own' && !empty($order->restaurant->midtrans_server_key)) {
                    Config::$serverKey    = $order->restaurant->midtrans_server_key;
                    Config::$isProduction = !str_starts_with(trim($order->restaurant->midtrans_server_key), 'SB-');
                }
            }

            // Sekarang SDK bisa instansiasi dengan key yang sesuai
            $notification = new Notification();

            $transaction  = $notification->transaction_status;
            $paymentType  = $notification->payment_type;
            $orderId      = $notification->order_id;
            $grossAmount  = (float) ($notification->gross_amount ?? 0);
            $fraud        = $notification->fraud_status;

            // Log::info("Midtrans Notification: $orderId - $transaction - $paymentType");

            // Check order type: Order or Subscription
            if (str_starts_with($orderId, 'SUB-')) {
                $this->handleSubscriptionPayment($orderId, $transaction, $fraud);
            } else {
                $this->handleOrderPayment($orderId, $transaction, $fraud, $paymentType, $grossAmount);
            }

            return response()->json(['message' => 'Notification processed']);
        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing notification'], 500);
        }
    }

    public function handleOrderPayment(
        string $midtransOrderId,
        string $transactionStatus,
        ?string $fraudStatus,
        string $paymentType,
        float $grossAmount
    ): void {
        // Extract real ID from format: ORD-{ID}-{TIMESTAMP} or {ID}-SPLIT-{TIMESTAMP}
        $parts   = explode('-', $midtransOrderId);
        if (count($parts) >= 2 && $parts[1] === 'SPLIT') {
            $orderId = $parts[0];
        } else {
            $orderId = (count($parts) >= 3 && $parts[0] === 'ORD') ? $parts[1] : $midtransOrderId;
        }

        $order = Order::with('restaurant')->find($orderId);

        if (!$order) {
            Log::warning("Order not found: $midtransOrderId");
            return;
        }

        // Jika restoran pakai akun Midtrans sendiri, re-init Config dengan key mereka
        // agar signature verification benar (jika dipanggil via webhook)
        $restaurant = $order->restaurant;
        if ($restaurant && $restaurant->gateway_mode === 'own' && !empty($restaurant->midtrans_server_key)) {
            Config::$serverKey    = $restaurant->midtrans_server_key;
            Config::$isProduction = !str_starts_with(trim($restaurant->midtrans_server_key), 'SB-');
        }

        $isPaid = false;
        
        if ($transactionStatus === 'capture' && $fraudStatus !== 'challenge') {
            $isPaid = true;
        } elseif ($transactionStatus === 'settlement') {
            $isPaid = true;
        }

        if ($isPaid) {
            // Check if this payment is already recorded (possibly as pending)
            $payment = \App\Models\OrderPayment::where('reference_number', $midtransOrderId)->first();
            
            if (!$payment) {
                // Record new payment if not exists
                $payment = \App\Models\OrderPayment::create([
                    'order_id' => $order->id,
                    'amount' => $grossAmount,
                    'payment_method' => $paymentType ?? 'qris',
                    'status' => 'paid',
                    'reference_number' => $midtransOrderId
                ]);

                // ── Update Saldo Restoran & Ledger ──
                if ($restaurant && $restaurant->gateway_mode === 'dineflo') {
                    $this->creditRestaurantBalance($restaurant, $order, $paymentType, $grossAmount);
                }
            } else {
                // If it already exists (pending), update it
                if ($payment->status !== 'paid') {
                    $payment->update([
                        'status' => 'paid',
                        'payment_method' => $paymentType ?? $payment->payment_method,
                    ]);

                    // Sync items marked for this payment as is_paid = true
                    foreach ($payment->items as $item) {
                        $item->update(['is_paid' => true]);
                    }

                    // ── Update Saldo Restoran & Ledger ──
                    if ($restaurant && $restaurant->gateway_mode === 'dineflo') {
                        $this->creditRestaurantBalance($restaurant, $order, $paymentType, $grossAmount);
                    }
                }
            }

            // Recalculate Total Paid
            $totalPaid = $order->orderPayments()->where('status', 'paid')->sum('amount');
            $newAmountPaid = $totalPaid;
            
            $isFullyPaid = $newAmountPaid >= $order->total_amount;

            $order->update([
                'amount_paid' => $newAmountPaid,
                'payment_status' => $isFullyPaid ? 'paid' : 'partial',
                'status' => $isFullyPaid ? 'confirmed' : 'pending',
                'payment_method' => $isFullyPaid ? ($paymentType ?? 'qris') : 'split',
            ]);
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            // Jika ditolak/kadaluarsa dan belum ada pembayaran valid, batalkan order
            if ($order->amount_paid <= 0) {
                $order->payment_status = 'failed';
                $order->status         = 'cancelled';
                $order->save();
            }
        }
    }

    /**
     * Catat kredit ke saldo restoran setelah pembayaran sukses.
     * Gross ditampilkan, fee dipotong, net yang masuk ke saldo.
     */
    protected function creditRestaurantBalance(
        \App\Models\Restaurant $restaurant,
        Order $order,
        string $paymentType,
        float $grossAmount
    ): void {
        $settings = app(\App\Settings\GeneralSettings::class);

        // 1. Gateway Fee (Midtrans)
        $gateway     = $this->calculateFee($paymentType, $grossAmount);
        $gatewayFee  = $gateway['fee_amount'];

        // 2. Platform Commission (Dineflo)
        $commissionPct = $settings->dineflo_commission_percentage ?? 0;
        $platformFee   = round($grossAmount * $commissionPct / 100);

        $totalFee  = $gatewayFee + $platformFee;
        $netAmount = $grossAmount - $totalFee;

        // Tambah saldo (net)
        $restaurant->increment('balance', $netAmount);

        // Catat ke ledger transparan
        RestaurantBalanceLedger::create([
            'restaurant_id'   => $restaurant->id,
            'order_id'        => $order->id,
            'type'            => 'credit',
            'payment_type'    => $paymentType,
            'gross_amount'    => $grossAmount,
            'fee_percentage'  => $gateway['percentage'] + $commissionPct,
            'fee_amount'      => $totalFee,
            'gateway_fee_amount'  => $gatewayFee,
            'platform_fee_amount' => $platformFee,
            'net_amount'      => $netAmount,
            'description'     => 'Pembayaran ' . RestaurantBalanceLedger::paymentTypeLabel($paymentType) . ' — Order #' . $order->order_number,
        ]);

        /*
        Log::info(sprintf(
            'Balance credited: Restaurant #%d | Order %s | Gross: %s | Fee: %s (%.2f%%) | Net: %s',
            $restaurant->id,
            $order->order_number,
            number_format($grossAmount, 0, ',', '.'),
            number_format($feeAmount, 0, ',', '.'),
            $fee['percentage'],
            number_format($netAmount, 0, ',', '.')
        ));
        */
    }

    protected function handleSubscriptionPayment(
        string $midtransOrderId,
        string $transactionStatus,
        ?string $fraudStatus
    ): void {
        // Format: SUB-{SUB_ID}-{TIMESTAMP}
        $parts = explode('-', $midtransOrderId);
        $subId = $parts[1] ?? null;

        if (!$subId) return;

        $subscription = Subscription::find($subId);
        if (!$subscription) {
            Log::warning("Subscription not found: $subId");
            return;
        }

        if (($transactionStatus === 'capture' && $fraudStatus === 'accept') || $transactionStatus === 'settlement') {
            $subscription->activate($midtransOrderId);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $subscription->status = 'cancelled';
            $subscription->save();
        }
    }
}
