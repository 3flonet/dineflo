<?php

namespace App\Filament\Restaurant\Pages\Pos\Traits;

/**
 * Trait HasGiftCard
 *
 * Mengelola state dan logic Gift Card di POS.
 */
trait HasGiftCard
{
    // ─── State ───────────────────────────────────────────────────────────────

    public $giftCardCode           = '';
    public $appliedGiftCard        = null;
    public $giftCardError          = '';
    public $giftCardDiscountAmount = 0;

    // ─── Computed ─────────────────────────────────────────────────────────────

    public function getGiftCardDiscountProperty()
    {
        // Jika ada discount amount dari order yang di-load (existing order), kembalikan itu
        if ($this->giftCardDiscountAmount > 0 && !$this->appliedGiftCard) {
            return $this->giftCardDiscountAmount;
        }

        if (!$this->appliedGiftCard) return 0;

        $card = \App\Models\GiftCard::find($this->appliedGiftCard['id']);
        if (!$card || !$card->isUsable()) return 0;

        // Total sebelum gift card diterapkan
        $currentTotal = max(0, $this->cartTotal - $this->voucherDiscount)
            + $this->additionalFees
            + $this->cartTax
            - $this->pointDiscount;

        // Diskon maksimal adalah sisa saldo kartu
        return min((float) $card->remaining_balance, $currentTotal);
    }

    // ─── Actions ──────────────────────────────────────────────────────────────

    public function applyGiftCard()
    {
        $this->giftCardError = '';
        $tenant = \Filament\Facades\Filament::getTenant();

        if (!$tenant->owner->hasFeature('Gift Cards')) {
            $this->giftCardError = 'Fitur Gift Card tidak tersedia.';
            return;
        }

        if (empty($this->giftCardCode)) {
            $this->appliedGiftCard = null;
            return;
        }

        $card = \App\Models\GiftCard::where('restaurant_id', $tenant->id)
            ->where('code', strtoupper($this->giftCardCode))
            ->first();

        if (!$card) {
            $this->giftCardError = 'Kode tidak valid.';
            $this->appliedGiftCard = null;
            return;
        }

        if (!$card->isUsable()) {
            $this->giftCardError = "Kartu tidak dapat digunakan ({$card->status_label}).";
            $this->appliedGiftCard = null;
            return;
        }

        $this->appliedGiftCard = $card->toArray();
        $this->giftCardCode    = strtoupper($this->giftCardCode);

        \Filament\Notifications\Notification::make()
            ->title('Gift Card Berhasil Dipasang')
            ->success()
            ->send();
    }

    public function removeGiftCard()
    {
        $this->appliedGiftCard        = null;
        $this->giftCardCode          = '';
        $this->giftCardError         = '';
        $this->giftCardDiscountAmount = 0;
    }
}
