<?php

namespace App\Filament\Restaurant\Pages\Pos\Traits;

/**
 * Trait HasSplitBill
 *
 * Mengelola state dan logic split bill (bayar terpisah) di POS.
 */
trait HasSplitBill
{
    // ─── State ───────────────────────────────────────────────────────────────

    public $isSplitBill        = false;
    public $splitType          = 'amount'; // 'amount' | 'item'
    public $splitAmount        = 0;
    public $selectedSplitItems = [];
    public $remainingBalance   = 0;

    // ─── Watchers ─────────────────────────────────────────────────────────────

    public function updatedSelectedSplitItems()
    {
        if ($this->isSplitBill && $this->splitType === 'item') {
            $this->splitAmount = $this->getSelectedItemsTotal();
        }
    }

    // ─── Actions ──────────────────────────────────────────────────────────────

    public function switchToSplitByItem()
    {
        if (!$this->existingOrderId) {
            $order = $this->saveOrder(true);
            if (!$order) {
                $this->splitType = 'amount'; // fallback
                return;
            }
        }
        $this->splitType = 'item';
    }

    public function getSelectedItemsTotal()
    {
        if (empty($this->selectedSplitItems)) {
            return 0;
        }

        return \App\Models\OrderItem::whereIn('id', $this->selectedSplitItems)
            ->where('is_paid', false)
            ->sum('total_price') ?? 0;
    }
}
