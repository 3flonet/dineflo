<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'refunded_items' => 'array',
        'is_full_refund' => 'boolean',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by_id');
    }
}
