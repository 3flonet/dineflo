<?php

namespace App\Filament\Restaurant\Pages\Pos\Traits;

/**
 * Trait HasCart
 *
 * Mengelola state cart, perhitungan total, dan manajemen item di POS.
 */
trait HasCart
{
    // ─── State ───────────────────────────────────────────────────────────────

    public $cart                  = [];
    public $search                = '';
    public $selectedCategory      = 'all';
    public $showVariantModal      = false;
    public $selectedItemForVariant = null;
    public $selectedVariantId     = null;
    public $selectedAddons        = [];
    public $itemNote              = '';
    public $showOrders            = false;
    public $cartGrandTotalState   = 0;

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    public function rendering()
    {
        $this->cartGrandTotalState   = $this->cartGrandTotal;
        $this->voucherDiscountAmount = $this->voucherDiscount;
        $this->pointDiscountAmount   = $this->pointDiscount;
        $this->giftCardDiscountAmount = $this->giftCardDiscount;
    }

    public function updatedSelectedCategory()
    {
        // reserved for future pagination reset
    }

    // ─── Computed Properties ───────────────────────────────────────────────────

    public function getCartTotalProperty()
    {
        return collect($this->cart)->sum('total_price');
    }

    public function getAdditionalFeesProperty()
    {
        $tenant    = \Filament\Facades\Filament::getTenant();
        $fees      = $tenant->additional_fees ?? [];
        $totalFees = 0;

        foreach ($fees as $fee) {
            if ($fee['type'] === 'fixed') {
                $totalFees += $fee['value'];
            } elseif ($fee['type'] === 'percentage') {
                $totalFees += ($this->cartTotal * ($fee['value'] / 100));
            }
        }

        return $totalFees;
    }

    public function getCartTaxProperty()
    {
        $tenant = \Filament\Facades\Filament::getTenant();

        if (!$tenant->tax_enabled) {
            return 0;
        }

        $taxPercentage = $tenant->tax_percentage ?? 10;
        return $this->cartTotal * ($taxPercentage / 100);
    }

    public function getCartGrandTotalProperty()
    {
        $baseTotal = max(0, $this->cartTotal - $this->voucherDiscount) + $this->additionalFees + $this->cartTax;
        return max(0, $baseTotal - $this->pointDiscount - $this->giftCardDiscount);
    }

    // ─── Add to Cart ──────────────────────────────────────────────────────────

    public function addToCart($itemId)
    {
        $tenant = \Filament\Facades\Filament::getTenant();

        // Prioritas: ambil dari cache, fallback ke DB
        $cachedItems = \App\Models\MenuItem::getCachedItems($tenant->id);
        $item        = $cachedItems->firstWhere('id', $itemId);

        if (!$item) {
            $item = \App\Models\MenuItem::with(['variants', 'addons'])->find($itemId);
        }

        if (!$item) return;

        if (!$item->relationLoaded('variants')) {
            $item->load(['variants', 'addons']);
        }

        // Jika ada variant atau addon → buka modal pilihan
        if ($item->variants->count() > 0 || $item->addons->count() > 0) {
            $this->selectedItemForVariant = $item;
            $this->selectedVariantId      = $item->variants->first()?->id;
            $this->selectedAddons         = [];
            $this->itemNote               = '';
            $this->showVariantModal       = true;
            return;
        }

        $this->addItemToCart($item);
    }

    public function confirmVariantSelection()
    {
        if (!$this->selectedItemForVariant) return;

        $variant = $this->selectedVariantId
            ? $this->selectedItemForVariant->variants->find($this->selectedVariantId)
            : null;

        $addons = \App\Models\MenuItemAddon::whereIn('id', $this->selectedAddons)->get();

        $this->addItemToCart($this->selectedItemForVariant, 1, $variant, $addons, $this->itemNote);

        $this->showVariantModal       = false;
        $this->selectedItemForVariant = null;
        $this->selectedVariantId      = null;
        $this->selectedAddons         = [];
    }

    protected function addItemToCart($item, $qty = 1, $variant = null, $addons = null, $note = '')
    {
        $basePrice     = $variant ? $variant->price : $item->price;
        $itemPrice     = $basePrice;
        $originalPrice = $itemPrice;
        $discountName  = null;

        $discount = $item->getActiveDiscount();
        if ($discount) {
            $discountName = $discount->name;
            if ($discount->type === 'percentage') {
                $itemPrice = $itemPrice - ($itemPrice * ($discount->value / 100));
            } else {
                $itemPrice = max(0, $itemPrice - $discount->value);
            }
        }

        $addonsPrice    = $addons ? $addons->sum('price') : 0;
        $totalUnitPrice = $itemPrice + $addonsPrice;
        $variantName    = $variant ? $variant->name : null;
        $addonsData     = $addons
            ? $addons->map(fn ($a) => ['id' => $a->id, 'name' => $a->name, 'price' => $a->price])->toArray()
            : [];

        // Cek stok habis
        if ($item->manage_stock && $item->stock_quantity <= 0) {
            \Filament\Notifications\Notification::make()
                ->title('Stok Habis')
                ->body("Menu {$item->name} sudah tidak tersedia.")
                ->danger()
                ->send();
            return;
        }

        // Cek duplikat (item + variant + addons yang sama)
        $existingKey = null;
        foreach ($this->cart as $key => $cartItem) {
            $isSameItem    = $cartItem['id'] == $item->id;
            $isSameVariant = ($cartItem['variant_id'] ?? null) == ($variant?->id ?? null);

            $cartAddonsIds = collect($cartItem['addons'])->pluck('id')->sort()->values()->toArray();
            $newAddonsIds  = collect($addonsData)->pluck('id')->sort()->values()->toArray();
            $isSameAddons  = $cartAddonsIds === $newAddonsIds;

            if ($isSameItem && $isSameVariant && $isSameAddons) {
                $existingKey = $key;
                break;
            }
        }

        if ($existingKey !== null) {
            // Cek stok untuk increment
            if ($item->manage_stock && ($this->cart[$existingKey]['quantity'] + $qty) > $item->stock_quantity) {
                \Filament\Notifications\Notification::make()
                    ->title('Stok Terbatas')
                    ->body("Hanya tersedia {$item->stock_quantity} porsi.")
                    ->warning()
                    ->send();
                return;
            }
            $this->cart[$existingKey]['quantity']   += $qty;
            $this->cart[$existingKey]['total_price'] = $this->cart[$existingKey]['quantity'] * $this->cart[$existingKey]['price'];
        } else {
            $this->cart[] = [
                'id'            => $item->id,
                'name'          => $item->name,
                'image'         => $item->image ? \Illuminate\Support\Facades\Storage::url($item->image) : null,
                'price'         => $totalUnitPrice,
                'original_price' => $originalPrice,
                'discount_name' => $discountName,
                'quantity'      => $qty,
                'variant_id'    => $variant ? $variant->id : null,
                'variant_name'  => $variantName,
                'total_price'   => $totalUnitPrice * $qty,
                'addons'        => $addonsData,
                'note'          => $note,
            ];
        }
    }

    // ─── Cart Management ──────────────────────────────────────────────────────

    public function removeItem($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
    }

    public function incrementItem($index)
    {
        $cartItem = $this->cart[$index];
        $tenant   = \Filament\Facades\Filament::getTenant();

        $cachedItems = \App\Models\MenuItem::getCachedItems($tenant->id);
        $menuItem    = $cachedItems->firstWhere('id', $cartItem['id']);

        if ($menuItem && $menuItem->manage_stock) {
            $freshStock    = \App\Models\MenuItem::where('id', $cartItem['id'])->value('stock_quantity');
            $totalCartQty  = collect($this->cart)->where('id', $cartItem['id'])->sum('quantity');

            if ($totalCartQty + 1 > $freshStock) {
                \Filament\Notifications\Notification::make()
                    ->title('Stok Terbatas')
                    ->body("Hanya tersedia {$freshStock} porsi {$menuItem->name}.")
                    ->warning()
                    ->send();
                return;
            }
        }

        $this->cart[$index]['quantity']++;
        $this->cart[$index]['total_price'] = $this->cart[$index]['quantity'] * $this->cart[$index]['price'];
    }

    public function decrementItem($index)
    {
        if ($this->cart[$index]['quantity'] > 1) {
            $this->cart[$index]['quantity']--;
            $this->cart[$index]['total_price'] = $this->cart[$index]['quantity'] * $this->cart[$index]['price'];
        } else {
            $this->removeItem($index);
        }
    }

    public function clearCart()
    {
        $this->cart         = [];
        $this->voucherId    = null;
        $this->voucherCode  = '';
        $this->voucherError = '';
    }
}
