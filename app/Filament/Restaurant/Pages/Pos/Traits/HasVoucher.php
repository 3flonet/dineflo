<?php

namespace App\Filament\Restaurant\Pages\Pos\Traits;

/**
 * Trait HasVoucher
 *
 * Mengelola state dan logic voucher/diskon code di POS.
 */
trait HasVoucher
{
    // ─── State ───────────────────────────────────────────────────────────────

    public $voucherCode           = '';
    public $voucherId             = null;
    public $voucherError          = '';
    public $voucherDiscountAmount = 0;

    // ─── Computed ─────────────────────────────────────────────────────────────

    public function getVoucherDiscountProperty()
    {
        if (!$this->voucherId) return 0;

        $voucher = \App\Models\Discount::find($this->voucherId);
        if (!$voucher) return 0;

        $discount       = 0;
        $activeSubtotal = $this->cartTotal;

        if ($voucher->scope !== 'all') {
            $eligibleTotal = collect($this->cart)->filter(function ($item) use ($voucher) {
                if ($voucher->scope === 'items') {
                    return in_array($item['id'], $voucher->menuItems->pluck('id')->toArray());
                }
                if ($voucher->scope === 'categories') {
                    $menuItem = \App\Models\MenuItem::find($item['id']);
                    return in_array($menuItem->menu_category_id, $voucher->menuCategories->pluck('id')->toArray());
                }
                return false;
            })->sum('total_price');

            $activeSubtotal = $eligibleTotal;
        }

        if ($voucher->type === 'percentage') {
            $discount = $activeSubtotal * ($voucher->value / 100);
        } else {
            $discount = min($activeSubtotal, $voucher->value);
        }

        return $discount;
    }

    // ─── Actions ──────────────────────────────────────────────────────────────

    public function applyVoucher()
    {
        $this->voucherError = '';
        $tenant = \Filament\Facades\Filament::getTenant();

        if (!auth()->user()->can('apply_voucher')) {
            $this->voucherError = 'Izin ditolak.';
            return;
        }

        if (!$tenant->owner->hasFeature('Voucher & Marketing')) {
            $this->voucherError = 'Fitur voucher tidak tersedia di paket langganan ini.';
            return;
        }

        if (empty($this->voucherCode)) {
            $this->voucherId = null;
            return;
        }

        $voucher = \App\Models\Discount::where('restaurant_id', $tenant->id)
            ->where('code', strtoupper($this->voucherCode))
            ->where('is_active', true)
            ->first();

        if (!$voucher) {
            $this->voucherError = 'Kode tidak valid.';
            $this->voucherId    = null;
            return;
        }

        if (!$voucher->isValidNow()) {
            $this->voucherError = 'Voucher kedaluwarsa.';
            $this->voucherId    = null;
            return;
        }

        if ($this->cartTotal < $voucher->min_order_amount) {
            $this->voucherError = 'Min belanja Rp' . number_format($voucher->min_order_amount, 0, ',', '.');
            $this->voucherId    = null;
            return;
        }

        if ($voucher->usage_limit && $voucher->total_usage >= $voucher->usage_limit) {
            $this->voucherError = 'Kuota habis.';
            $this->voucherId    = null;
            return;
        }

        if ($voucher->target_type !== 'all') {
            $member = \App\Models\Member::where('restaurant_id', $tenant->id)
                ->where('whatsapp', $this->customerPhone)
                ->first();

            if (!$member) {
                $this->voucherError = 'Gunakan nomor WA member.';
                $this->voucherId    = null;
                return;
            }

            if ($voucher->target_type === 'tiers_only') {
                $memberTier    = strtolower($member->tier ?? 'bronze');
                $eligibleTiers = array_map('strtolower', $voucher->target_tiers ?? []);
                if (!in_array($memberTier, $eligibleTiers)) {
                    $this->voucherError = 'Hanya untuk tier: ' . implode(', ', $eligibleTiers);
                    $this->voucherId    = null;
                    return;
                }
            }
        }

        $this->voucherId   = $voucher->id;
        $this->voucherCode = strtoupper($this->voucherCode);

        \Filament\Notifications\Notification::make()
            ->title('Voucher Berhasil Dipasang')
            ->success()
            ->send();
    }

    public function removeVoucher()
    {
        $this->voucherId   = null;
        $this->voucherCode = '';
        $this->voucherError = '';
    }
}
