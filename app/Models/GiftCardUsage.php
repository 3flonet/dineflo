<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftCardUsage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'gift_card_id',
        'order_id',
        'amount_used',
        'balance_before',
        'balance_after',
        'used_at',
    ];

    protected $casts = [
        'amount_used'    => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after'  => 'decimal:2',
        'used_at'        => 'datetime',
    ];

    public function giftCard(): BelongsTo
    {
        return $this->belongsTo(GiftCard::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
