<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use \App\Traits\BelongsToTenant, \App\Traits\NormalizesPhone;
    protected $fillable = [
        'restaurant_id',
        'table_id',
        'member_id',
        'processed_by_id',
        'served_by_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'tax_amount',
        'additional_fees_amount',
        'additional_fees_details',
        'voucher_discount_amount',
        'voucher_code',
        'discount_id',
        'points_used',
        'points_discount_amount',
        'gift_card_discount_amount',
        'total_amount',
        'amount_paid',
        'is_split_bill',
        'split_type',
        'is_loyalty_processed',
        'is_stock_deducted',
        'feedback_hash',
        'tracking_hash',
        'cooking_started_at',
        'cooking_finished_at',
        'served_at',
        'refunded_amount',
        'refund_status',
        'notes',
        'order_type',
        'payment_token',
        'payment_url',
        'midtrans_transaction_id',
        'offline_id',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'is_split_bill' => 'boolean',
        'amount_paid' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'additional_fees_amount' => 'decimal:2',
        'voucher_discount_amount' => 'decimal:2',
        'additional_fees_details' => 'array',
        'is_loyalty_processed' => 'boolean',
        'is_stock_deducted' => 'boolean',
        'points_used' => 'integer',
        'points_discount_amount' => 'decimal:2',
        'gift_card_discount_amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'cooking_started_at' => 'datetime',
        'cooking_finished_at' => 'datetime',
        'served_at' => 'datetime',
    ];

    public function getCookingDurationMinutesAttribute()
    {
        if (!$this->cooking_started_at || !$this->cooking_finished_at) {
            return null;
        }

        return $this->cooking_started_at->diffInMinutes($this->cooking_finished_at);
    }

    public function refundLogs()
    {
        return $this->hasMany(RefundLog::class);
    }

    protected $dispatchesEvents = [
        'created' => \App\Events\OrderCreated::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($order) {
            if ($order->customer_phone) {
                $order->customer_phone = $order->normalizePhoneNumber($order->customer_phone);
            }
        });

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(\Illuminate\Support\Str::random(10));
            }
            if (empty($order->feedback_hash)) {
                $order->feedback_hash = \Illuminate\Support\Str::random(32);
            }
            if (empty($order->tracking_hash)) {
                $order->tracking_hash = \Illuminate\Support\Str::random(40);
            }
        });
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderPayments()
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function generateWhatsAppMessage(): string
    {
        $restaurant = $this->restaurant;
        $items = $this->items()->with('menuItem')->get();
        
        $message = "*NOTA PESANAN - {$restaurant->name}* 🧾\n\n";
        $message .= "Halo Kak *" . ($this->customer_name ?: 'Pelanggan') . "*, terima kasih sudah berkunjung! Berikut adalah rincian pesanan Anda:\n\n";
        $message .= "*ID Pesanan:* #{$this->order_number}\n";
        $message .= "*Tanggal:* " . ($this->created_at->format('d M Y H:i')) . "\n\n";
        
        $message .= "*Detail Pesanan:*\n";
        $message .= "------------------------------------------\n";
        
        foreach ($items as $item) {
            $message .= "- {$item->quantity}x _{$item->menuItem->name}_ (Rp " . number_format($item->total_price, 0, ',', '.') . ")\n";
        }
        
        $message .= "------------------------------------------\n";
        $message .= "Subtotal: Rp " . number_format($this->subtotal, 0, ',', '.') . "\n";

        // Additional Fees
        if ($this->additional_fees_details && is_array($this->additional_fees_details)) {
            foreach ($this->additional_fees_details as $fee) {
                $feeAmount = ($fee['type'] ?? '') === 'fixed' ? $fee['value'] : ($this->subtotal * ($fee['value'] / 100));
                if ($feeAmount > 0) {
                    $message .= "{$fee['name']}: Rp " . number_format($feeAmount, 0, ',', '.') . "\n";
                }
            }
        }

        // Tax
        if ($this->tax_amount > 0) {
            $message .= "Pajak: Rp " . number_format($this->tax_amount, 0, ',', '.') . "\n";
        }

        // Discounts
        if ($this->voucher_discount_amount > 0) {
            $message .= "Diskon Voucher: -Rp " . number_format($this->voucher_discount_amount, 0, ',', '.') . "\n";
        }
        if ($this->points_discount_amount > 0) {
            $message .= "Poin Loyalitas: -Rp " . number_format($this->points_discount_amount, 0, ',', '.') . "\n";
        }
        if ($this->gift_card_discount_amount > 0) {
            $message .= "Gift Card: -Rp " . number_format($this->gift_card_discount_amount, 0, ',', '.') . "\n";
        }
        
        $message .= "------------------------------------------\n";
        $message .= "*Total Bayar: Rp " . number_format($this->total_amount, 0, ',', '.') . "*\n\n";
        
        $message .= "*Metode Bayar:* " . strtoupper($this->payment_method ?: 'Tunai') . " (" . ($this->payment_status === 'paid' ? 'Lunas ✅' : 'Belum Bayar ❌') . ")\n\n";
        
        // Link Nota Digital & Tracking
        $receiptUrl = route('order.public_receipt', ['order' => $this->id]); 
        $trackingUrl = route('order.track', ['hash' => $this->tracking_hash]);

        $message .= "Lihat nota digital di sini:\n🔗 {$receiptUrl}\n\n";
        $message .= "Lacak status pesanan Anda (Live):\n🔗 {$trackingUrl}\n\n";

        // Link Portal Member (jika order punya member terkait)
        if ($this->member_id) {
            $portalUrl = route('member.portal.login', ['restaurant' => $restaurant->slug]);
            $message  .= "👤 *Portal Member Anda:*\n🔗 {$portalUrl}\n";
            $message  .= "_Cek poin, tier, dan histori belanja Anda di sana._\n\n";
        }

        $message .= "_Terima kasih atas kunjungannya, kami tunggu kedatangan Anda kembali!_";

        return $message;
    }
    public function generateFeedbackWhatsAppMessage(): string
    {
        $restaurant = $this->restaurant;
        $reviewUrl = route('order.feedback', ['hash' => $this->feedback_hash]);

        $message = "*TERIMA KASIH - {$restaurant->name}* 🙏\n\n";
        $message .= "Halo Kak *" . ($this->customer_name ?: 'Pelanggan') . "*, terima kasih telah memesan di *" . $restaurant->name . "*.\n\n";
        $message .= "Kepuasan Anda adalah prioritas kami. Mohon luangkan waktu sebentar untuk memberikan penilaian atas hidangan dan pelayanan kami melalui link berikut:\n\n";
        $message .= "🔗 " . $reviewUrl . "\n\n";
        $message .= "Masukan Anda sangat berarti bagi kami untuk terus memberikan yang terbaik.\n\n";

        // Link Portal Member
        if ($this->member_id) {
            $portalUrl = route('member.portal.login', ['restaurant' => $restaurant->slug]);
            $message  .= "👤 *Portal Member:* {$portalUrl}\n";
            $message  .= "_Cek poin & histori belanja Anda._\n\n";
        }

        $message .= "_Sampai jumpa kembali!_";

        return $message;
    }

    public function generateRefundWhatsAppMessage(float $refundAmount, string $reason): string
    {
        $restaurant = $this->restaurant;
        $type = ($this->refund_status === 'full') ? 'PENUH (Full Refund)' : 'SEBAGIAN (Partial Refund)';

        $message = "*KONFIRMASI REFUND - {$restaurant->name}* 🔄\n\n";
        $message .= "Halo Kak *" . ($this->customer_name ?: 'Pelanggan') . "*, kami ingin memberitahukan bahwa pengembalian dana untuk pesanan Anda telah diproses.\n\n";
        $message .= "*ID Pesanan:* #{$this->order_number}\n";
        $message .= "*Jenis Refund:* {$type}\n";
        $message .= "*Alasan:* {$reason}\n";
        $message .= "*Jumlah Dikembalikan:* Rp " . number_format($refundAmount, 0, ',', '.') . "\n\n";
        $message .= "Dana akan dikembalikan melalui metode pembayaran awal Anda. Proses mungkin membutuhkan beberapa waktu tergantung pada bank/penyedia pembayaran.\n\n";

        $receiptUrl = route('order.public_receipt', ['order' => $this->id]);
        $message .= "Lihat nota pesanan Anda:\n🔗 {$receiptUrl}\n\n";
        $message .= "_Terima kasih atas pengertian Anda. Kami mohon maaf atas ketidaknyamanan ini._";

        return $message;
    }

    public function feedback()
    {
        return $this->hasOne(OrderFeedback::class);
    }
}
