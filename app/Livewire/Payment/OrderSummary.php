<?php

namespace App\Livewire\Payment;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\MidtransService;

#[Layout('components.layouts.app')]
class OrderSummary extends Component
{
    public Order $order;
    public $snapToken;

    public function mount(Order $order)
    {
        $this->order = $order;
        
        // Ensure user owns this order or session logic (skipped for now for simplicity)
        // If paid, show success
        
        if ($this->order->payment_status !== 'paid' && $this->order->payment_method === 'midtrans' && $this->order->total_amount > 0) {
            $this->ensureSnapToken();
        }
    }

    public function getWhatsappUrlProperty()
    {
        if ($this->order->payment_method === 'midtrans') {
            return null;
        }

        $message = "Halo *" . $this->order->restaurant->name . "*,\n";
        $message .= "Saya ingin mengonfirmasi pesanan saya:\n\n";

        foreach ($this->order->items as $item) {
            $variant = $item->variant ? " (" . $item->variant->name . ")" : "";
            $addons = null;
            if ($item->addons) {
                // Ensure addons is treated properly depending on cast type (usually JSON to array)
                $addonsArray = is_string($item->addons) ? json_decode($item->addons, true) : $item->addons;
                if (!empty($addonsArray) && is_array($addonsArray)) {
                    $addons = "\n  + " . implode(", ", array_column($addonsArray, 'name'));
                }
            }
            $message .= "- " . $item->quantity . "x " . $item->menuItem->name . $variant . $addons . "\n";
        }

        // Add additional detail like order number etc
        $message .= "\nTotal: *Rp " . number_format($this->order->total_amount, 0, ',', '.') . "*\n";
        $message .= "----------------\n";
        $message .= "Nama: " . $this->order->customer_name . "\n";
        $message .= "Order ID: #" . $this->order->order_number . "\n";
        $message .= "Pembayaran: Tunai / Kasir\n";
        
        if ($this->order->table) {
            $message .= "Meja: " . $this->order->table->name . " (" . $this->order->table->area . ")\n";
        }

        if ($this->order->notes) {
            $message .= "\nCatatan:\n" . $this->order->notes . "\n";
        }

        $phoneNumber = $this->order->restaurant->phone ?? '6281234567890';
        $encodedMessage = urlencode($message);
        
        return "https://wa.me/{$phoneNumber}?text={$encodedMessage}";
    }

    public function ensureSnapToken()
    {
        // If token exists, use it. If not, create new.
        if (empty($this->order->payment_token)) {
            $midtrans = new MidtransService($this->order->restaurant);
            $this->snapToken = $midtrans->createSnapToken($this->order);
            
            if ($this->snapToken) {
                $this->order->payment_token = $this->snapToken;
                $this->order->save();
            }
        } else {
            $this->snapToken = $this->order->payment_token;
        }
    }

    // Called by JS after successful payment
    public function handlePaymentSuccess($result)
    {
        // For production, verify with Midtrans API again here using Server Key
        // But for MVP, let's update status and ensure Ledgers are captured locally
        // We reuse handleOrderPayment so accounting logic is DRY for missing webhooks.
        
        try {
            $midtransOrderId   = $result['order_id'] ?? $this->order->id;
            $transactionStatus = $result['transaction_status'] ?? 'settlement';
            $fraudStatus       = $result['fraud_status'] ?? 'accept';
            $paymentType       = $result['payment_type'] ?? 'qris';
            $grossAmount       = (float) ($result['gross_amount'] ?? $this->order->total_amount);

            app(\App\Http\Controllers\MidtransController::class)->handleOrderPayment(
                $midtransOrderId,
                $transactionStatus,
                $fraudStatus,
                $paymentType,
                $grossAmount
            );
        } catch (\Exception $e) {
            \Log::error('Local Midtrans Success Callback Error: ' . $e->getMessage());
        }

        $this->order->refresh();
        session()->flash('success', 'Payment Successful! Thank you for your order.');
    }

    public function render()
    {
        return view('livewire.payment.order-summary')
            ->layoutData(['restaurant' => $this->order->restaurant]);
    }
}
