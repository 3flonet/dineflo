<?php

namespace App\Actions\Pos;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\OrderItem;
use App\Models\CashDrawerLog;
use App\Models\PosRegisterSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProcessCheckoutAction
{
    /**
     * @param Order $order
     * @param float $amountToPay
     * @param string $paymentMethod
     * @param array $selectedItemIds Untuk split by item
     * @param string|null $referenceNumber Untuk kartu/QRIS reference
     * @return array [isFullyPaid, OrderPayment]
     */
    public function execute(
        Order $order,
        float $amountToPay,
        string $paymentMethod = 'cash',
        array $selectedItemIds = [],
        ?string $referenceNumber = null,
        array $extraData = []
    ): array {
        return DB::transaction(function () use ($order, $amountToPay, $paymentMethod, $selectedItemIds, $referenceNumber, $extraData) {
            
            $mdrFee = 0;
            $netAmount = $amountToPay;
            $bankName = $extraData['bank_name'] ?? null;

            // Logika Perhitungan MDR untuk EDC
            if ($paymentMethod === 'edc' && $bankName) {
                $edcConfig = $order->restaurant->edc_config ?? [];
                $bankConfig = collect($edcConfig)->firstWhere('bank_name', $bankName);
                if ($bankConfig) {
                    $mdrPercent = (float) ($bankConfig['mdr_percent'] ?? 0);
                    $mdrFee = ($amountToPay * ($mdrPercent / 100));
                    $netAmount = $amountToPay - $mdrFee;
                }
            }

            // 1. Create Payment Record
            $payment = OrderPayment::create([
                'order_id'         => $order->id,
                'amount'           => $amountToPay,
                'payment_method'   => $paymentMethod,
                'status'           => 'paid',
                'reference_number' => $referenceNumber,
                'bank_name'        => $bankName,
                'mdr_fee_amount'   => $mdrFee,
                'net_amount'       => $netAmount,
            ]);

            // 2. Mark items as paid (for Split by Item)
            if (!empty($selectedItemIds)) {
                OrderItem::whereIn('id', $selectedItemIds)
                    ->where('order_id', $order->id)
                    ->update([
                        'is_paid'          => true,
                        'order_payment_id' => $payment->id
                    ]);
            }

            // 3. Cash Drawer Logic (if method is cash)
            if ($paymentMethod === 'cash') {
                $session = PosRegisterSession::where('restaurant_id', $order->restaurant_id)
                    ->where('status', 'open')
                    ->first();

                if ($session) {
                    CashDrawerLog::create([
                        'restaurant_id'           => $order->restaurant_id,
                        'user_id'                 => Auth::id(),
                        'order_id'                => $order->id,
                        'pos_register_session_id' => $session->id,
                        'amount'                  => $amountToPay,
                        'type'                    => 'automatic',
                        'reason'                  => "Pembayaran Pesanan #{$order->order_number}",
                    ]);

                    $session->increment('expected_cash', $amountToPay);
                }
            }

            // 4. Update Order Totals and Status
            $totalPaid = $order->orderPayments()->where('status', 'paid')->sum('amount');
            $isFullyPaid = $totalPaid >= $order->total_amount;

            $order->update([
                'amount_paid'    => $totalPaid,
                'payment_status' => $isFullyPaid ? 'paid' : 'partial',
                'status'         => $isFullyPaid ? 'confirmed' : 'pending',
                'payment_method' => $isFullyPaid ? $paymentMethod : 'split',
                'processed_by_id' => Auth::id(),
            ]);

            return [$isFullyPaid, $payment];
        });
    }
}
