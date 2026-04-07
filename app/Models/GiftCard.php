<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class GiftCard extends Model
{
    protected $fillable = [
        'restaurant_id',
        'created_by',
        'code',
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'personal_message',
        'original_amount',
        'remaining_balance',
        'status',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'original_amount'   => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'expires_at'        => 'datetime',
        'used_at'           => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(GiftCardUsage::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForRestaurant($query, int $restaurantId)
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Generate a unique gift card code like GC-ABCDE-12345
     */
    public static function generateCode(): string
    {
        do {
            $code = 'GC-' . strtoupper(Str::random(5)) . '-' . strtoupper(Str::random(5));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    /**
     * Check if this gift card is usable right now.
     */
    public function isUsable(): bool
    {
        if ($this->status !== 'active') return false;
        if ($this->remaining_balance <= 0) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;

        return true;
    }

    /**
     * Apply amount to this gift card and record usage.
     * Returns the actual amount deducted.
     */
    public function applyAmount(float $amount, ?int $orderId = null): float
    {
        $amountToDeduct = min($amount, (float) $this->remaining_balance);
        $balanceBefore  = (float) $this->remaining_balance;
        $balanceAfter   = $balanceBefore - $amountToDeduct;

        // Record usage
        $this->usages()->create([
            'order_id'       => $orderId,
            'amount_used'    => $amountToDeduct,
            'balance_before' => $balanceBefore,
            'balance_after'  => $balanceAfter,
            'used_at'        => now(),
        ]);

        // Update balance
        $this->remaining_balance = $balanceAfter;

        if ($balanceAfter <= 0) {
            $this->status  = 'used';
            $this->used_at = now();
        }

        $this->save();

        return $amountToDeduct;
    }

    /**
     * Expire overdue gift cards. Called by scheduler or manually.
     */
    public static function expireOverdue(): int
    {
        return static::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->original_amount, 0, ',', '.');
    }

    public function getFormattedBalanceAttribute(): string
    {
        return 'Rp ' . number_format($this->remaining_balance, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'    => 'Aktif',
            'used'      => 'Habis Digunakan',
            'expired'   => 'Kadaluarsa',
            'cancelled' => 'Dibatalkan',
            default     => 'Unknown',
        };
    }
}
