<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantBalanceLedger extends Model
{
    protected $table = 'restaurant_balance_ledger';

    protected $guarded = [];

    protected $casts = [
        'gross_amount'   => 'decimal:2',
        'fee_percentage' => 'decimal:2',
        'fee_amount'     => 'decimal:2',
        'gateway_fee_amount'  => 'decimal:2',
        'platform_fee_amount' => 'decimal:2',
        'net_amount'     => 'decimal:2',
    ];

    // ── Relationships ────────────────────────────────────────

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function withdrawRequest()
    {
        return $this->belongsTo(WithdrawRequest::class);
    }

    // ── Helpers ──────────────────────────────────────────────

    public function isCredit(): bool
    {
        return $this->type === 'credit';
    }

    public function isDebit(): bool
    {
        return $this->type === 'debit';
    }

    public static function paymentTypeLabel(string $type): string
    {
        return match($type) {
            'qris', 'other_qris'  => 'QRIS',
            'gopay'               => 'GoPay',
            'shopeepay'           => 'ShopeePay',
            'bank_transfer'       => 'Virtual Account',
            'credit_card'         => 'Kartu Kredit',
            'cstore'              => 'Minimarket',
            default               => strtoupper($type),
        };
    }
}
