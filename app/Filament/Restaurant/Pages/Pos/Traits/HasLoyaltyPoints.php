<?php

namespace App\Filament\Restaurant\Pages\Pos\Traits;

/**
 * Trait HasLoyaltyPoints
 *
 * Mengelola state dan logic redemption loyalty points di POS.
 */
trait HasLoyaltyPoints
{
    // ─── State ───────────────────────────────────────────────────────────────

    public $usePoints           = false;
    public $pointsToUse         = 0;
    public $initialPoints       = 0;
    public $pointDiscountAmount = 0;

    // ─── Computed ─────────────────────────────────────────────────────────────

    public function getPointDiscountProperty()
    {
        if (!$this->usePoints || !$this->pointsToUse) return 0;

        $tenant     = \Filament\Facades\Filament::getTenant();
        $pointValue = $tenant->loyalty_point_redemption_value ?: 0;

        return $this->pointsToUse * $pointValue;
    }

    // ─── Watchers ─────────────────────────────────────────────────────────────

    public function updatedUsePoints($value)
    {
        if (!$value) {
            $this->pointsToUse = 0;
        } else {
            // Default ke semua poin yang tersedia (atau yang dibutuhkan)
            $this->updatedPointsToUse(($this->member?->points_balance ?? 0) + $this->initialPoints);
        }
    }

    public function updatedPointsToUse($value)
    {
        if (!$this->isMember || !$this->member) {
            $this->pointsToUse = 0;
            return;
        }

        // Maksimal poin = saldo member + poin yang sudah di-commit ke order ini
        $maxPoints = $this->member->points_balance + $this->initialPoints;

        // Tidak boleh melebihi total yang dibutuhkan
        $tenant     = \Filament\Facades\Filament::getTenant();
        $pointValue = $tenant->loyalty_point_redemption_value ?: 0;

        if ($pointValue > 0) {
            $currentTotalBeforePoints    = max(0, $this->cartTotal - $this->voucherDiscount) + $this->additionalFees + $this->cartTax;
            $maxPossiblePointsForTotal   = ceil($currentTotalBeforePoints / $pointValue);
            $maxPoints                   = min($maxPoints, $maxPossiblePointsForTotal);
        }

        if ($value > $maxPoints) {
            $this->pointsToUse = $maxPoints;
        }

        if ($this->pointsToUse < 0) $this->pointsToUse = 0;
    }
}
