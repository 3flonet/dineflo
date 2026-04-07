<?php

namespace App\Filament\Restaurant\Pages;

use Filament\Pages\Page;
use App\Actions\Pos\SaveOrderAction;
use App\Actions\Pos\ProcessCheckoutAction;
use App\Filament\Restaurant\Pages\Pos\Traits\HasCart;
use App\Filament\Restaurant\Pages\Pos\Traits\HasVoucher;
use App\Filament\Restaurant\Pages\Pos\Traits\HasGiftCard;
use App\Filament\Restaurant\Pages\Pos\Traits\HasLoyaltyPoints;
use App\Filament\Restaurant\Pages\Pos\Traits\HasSplitBill;
use App\Filament\Restaurant\Pages\Pos\Traits\HasCashRegister;

class Pos extends Page
{
    use HasCart,
        HasVoucher,
        HasGiftCard,
        HasLoyaltyPoints,
        HasSplitBill,
        HasCashRegister,
        \App\Traits\NormalizesPhone;

    protected static ?string $navigationIcon      = 'heroicon-o-computer-desktop';
    protected static string  $view                = 'filament.restaurant.pages.pos';
    protected static ?string $slug                = 'pos';
    protected static bool    $shouldRegisterNavigation = false;
    protected static string  $layout             = 'filament-panels::components.layout.simple';

    // ─── Core State (tidak masuk ke trait manapun) ────────────────────────────

    public $existingOrderId  = null;
    public $selectedTableId  = null;
    public $customerName     = '';
    public $customerPhone    = '';
    public $isMember         = false;
    public $member           = null;
    public $wantsToRegister  = false;
    public $paymentMethod    = null;
    public $selectedBank     = null;
    public $edcReference     = '';

    // ─── Access Control ───────────────────────────────────────────────────────

    public static function canAccess(): bool
    {
        return auth()->user()->can('page_Pos') && auth()->user()->hasFeature('POS System');
    }

    // ─── Member Detection ──────────────────────────────────────────────────────

    public function updatedCustomerPhone($value)
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        if (!$tenant->owner->hasFeature('Membership & Loyalty')) {
            return;
        }

        $normalizedPhone = $this->normalizePhoneNumber($value);

        if ($normalizedPhone && strlen($normalizedPhone) >= 10) {
            $this->member = \App\Models\Member::where('restaurant_id', $tenant->id)
                ->where('whatsapp', $normalizedPhone)
                ->first();

            if ($this->member) {
                $this->isMember         = true;
                $this->customerName     = $this->member->name;
                $this->wantsToRegister  = false;
            } else {
                $this->isMember = false;
                $this->member   = null;
            }
        } else {
            $this->isMember        = false;
            $this->member          = null;
            $this->wantsToRegister = false;
        }
    }

    // ─── Mount ────────────────────────────────────────────────────────────────

    public function mount()
    {
        $this->cart = [];

        $orderId = request()->query('load_order');
        if ($orderId) $this->showOrders = false;
        $this->fetchOrderForCart($orderId);
    }

    public function selectOrder($orderId)
    {
        $this->fetchOrderForCart($orderId);
        $this->showOrders = false;
    }

    protected function fetchOrderForCart($orderId)
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        $order = \App\Models\Order::where('restaurant_id', $tenant->id)
            ->with(['items.menuItem', 'items.variant'])
            ->find($orderId);

        if (!$order) {
            \Filament\Notifications\Notification::make()
                ->title('Order not found or not editable')
                ->danger()
                ->send();
            return;
        }

        // Hitung nominal yang sudah dibayar (bisa jadi partial/sudah split bill tapi belum tuntas)
        $totalPaid = $order->orderPayments()->where('status', 'paid')->sum('amount');
        $this->remainingBalance = max(0, $order->total_amount - $totalPaid);
        if ($this->remainingBalance > 0 && $this->remainingBalance < $order->total_amount) {
            $this->isSplitBill = true;
            $this->splitAmount = $this->remainingBalance;
        } else {
            $this->isSplitBill = false;
            $this->splitAmount = 0;
            $this->remainingBalance = 0;
        }

        if ($order->payment_status === 'paid') {
            \Filament\Notifications\Notification::make()
                ->title('Order already paid')
                ->warning()
                ->send();
            return;
        }

        $this->existingOrderId = $order->id;
        $this->customerName = $order->customer_name;
        $this->customerPhone = $order->customer_phone;
        $this->selectedTableId = $order->table_id ?? 'takeaway';
        
        // Load Voucher Data
        $this->voucherId = $order->discount_id;
        $this->voucherCode = $order->voucher_code;
        $this->voucherDiscountAmount = $order->voucher_discount_amount;

        // Load Loyalty Points Data
        if ($order->points_used > 0) {
            $this->usePoints = true;
            $this->pointsToUse = $order->points_used;
            $this->initialPoints = $order->points_used;
        } else {
            $this->usePoints = false;
            $this->pointsToUse = 0;
            $this->initialPoints = 0;
        }

        // Load Gift Card Data
        $this->giftCardDiscountAmount = $order->gift_card_discount_amount ?? 0;

        // Trigger member detection logic
        if ($order->member_id) {
            $this->member = $order->member;
            $this->isMember = true;
            $this->wantsToRegister = false;
        } elseif ($this->customerPhone) {
            $this->updatedCustomerPhone($this->customerPhone);
        } else {
            $this->isMember = false;
            $this->member = null;
            $this->wantsToRegister = false;
        }

        $this->cart = [];
        foreach ($order->items as $item) {
            $this->cart[] = [
                'id' => $item->menu_item_id,
                'name' => $item->menuItem->name,
                'image' => $item->menuItem->image ? \Illuminate\Support\Facades\Storage::url($item->menuItem->image) : null,
                'price' => $item->unit_price,
                'quantity' => $item->quantity,
                'variant_id' => $item->menu_item_variant_id,
                'variant_name' => $item->variant?->name,
                'total_price' => $item->total_price,
                'addons' => $item->addons ?? [],
                'note' => $item->note,
            ];
        }
    }

    public function getIsPastClosingProperty()
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        $now = now();
        $day = strtolower($now->format('l'));
        $hours = collect($tenant->opening_hours)->firstWhere('day', $day);

        if (!$hours || $hours['is_closed']) return false;

        try {
            $closeTime = \Carbon\Carbon::createFromFormat('H:i', $hours['close']);
            $openTime = \Carbon\Carbon::createFromFormat('H:i', $hours['open']);
            
            $closeDateTime = $now->copy()->setTimeFromTimeString($hours['close']);
            
            if ($closeTime->lt($openTime)) {
                if ($now->hour >= $openTime->hour) {
                    $closeDateTime->addDay();
                }
            }

            return $now->greaterThan($closeDateTime);
        } catch (\Exception $e) {
            return false;
        }
    }


    // ─── Order Loading ────────────────────────────────────────────────────────

    public function saveOrder(SaveOrderAction $saveOrderAction, $silent = false)
    {
        if (empty($this->cart)) return null;
        
        $tenant = \Filament\Facades\Filament::getTenant();

        if (!$this->selectedTableId && $this->selectedTableId !== 'takeaway') {
            if (!$silent) {
                \Filament\Notifications\Notification::make()
                    ->title('Please select a table')
                    ->danger()
                    ->send();
            }
            return null;
        }

        if (!empty($this->customerPhone) && empty($this->customerName)) {
            if (!$silent) {
                \Filament\Notifications\Notification::make()
                    ->title('Nama Pelanggan wajib diisi jika Nomor WhatsApp diisi')
                    ->danger()
                    ->send();
            }
            return null;
        }

        $order = $saveOrderAction->execute(
            $tenant,
            $this->cart,
            [
                'table_id'                  => $this->selectedTableId === 'takeaway' ? null : $this->selectedTableId,
                'customer_name'             => $this->customerName,
                'customer_phone'            => $this->customerPhone,
                'subtotal'                  => $this->cartTotal,
                'total_amount'              => $this->cartGrandTotal,
                'discount_id'               => $this->voucherId,
                'voucher_code'              => $this->voucherCode,
                'voucher_discount_amount'   => $this->voucherDiscount,
                'tax_amount'                => $this->cartTax,
                'additional_fees_amount'    => $this->additionalFees,
                'points_used'               => $this->usePoints ? $this->pointsToUse : 0,
                'points_discount_amount'    => $this->pointDiscount,
                'gift_card_discount_amount' => $this->giftCardDiscount,
                'payment_method'            => $this->paymentMethod ?? 'cash',
                'is_split_bill'             => $this->isSplitBill,
            ],
            $this->existingOrderId,
            $this->member,
            $this->wantsToRegister
        );

        $this->existingOrderId = $order->id;
        $this->isMember = $order->member_id ? true : false;
        if ($order->member_id && !$this->member) {
            $this->member = $order->member;
        }

        if (!$silent) {
            \Filament\Notifications\Notification::make()
                ->title('Pesanan berhasil disimpan')
                ->success()
                ->send();
        }
        
        return $order;
    }


    /**
     * @param SaveOrderAction $saveOrderAction
     * @param ProcessCheckoutAction $checkoutAction
     * @return void
     */
    public function processCheckout(SaveOrderAction $saveOrderAction, ProcessCheckoutAction $checkoutAction)
    {
        $order = $this->saveOrder($saveOrderAction, true);
        if (!$order) return;

        // --- SPLIT BY ITEM LOGIC PRE-PROCESSING ---
        $totalAmountToPay = $this->isSplitBill ? $this->splitAmount : $this->cartGrandTotal;
        $activeSplitItems = [];

        if ($this->isSplitBill && $this->splitType === 'item') {
            $totalAmountToPay = \App\Models\OrderItem::whereIn('id', $this->selectedSplitItems)
                ->where('order_id', $order->id)
                ->where('is_paid', false)
                ->sum('total_price');
        }

        // --- Execute Checkout Action ---
        // Penanganan Khusus: Jika total bayar Rp 0 (karena poin/gift card), langsung bayar tanpa gateway
        if ($totalAmountToPay <= 0) {
            $specialMethod = 'loyalty_giftcard';
            if ($this->pointsToUse > 0) $specialMethod = 'points';
            elseif ($this->giftCardDiscount > 0) $specialMethod = 'gift_card';
            
            [$isFullyPaid, $payment] = $checkoutAction->execute(
                $order,
                $totalAmountToPay,
                $specialMethod,
                ($this->isSplitBill && $this->splitType === 'item') ? $this->selectedSplitItems : []
            );

            // Success Notification
            \Filament\Notifications\Notification::make()
                ->title('Pesanan Berhasil (Lunas via Poin/Gift Card)')
                ->success()
                ->send();

            // Lanjut ke pembersihan state... (logika yang sama dengan cash di bawah)
            $this->finalizePayment($order, $payment, $totalAmountToPay, $isFullyPaid);

        } elseif ($this->paymentMethod === 'cash') {
            
            [$isFullyPaid, $payment] = $checkoutAction->execute(
                $order,
                $totalAmountToPay,
                'cash',
                ($this->isSplitBill && $this->splitType === 'item') ? $this->selectedSplitItems : []
            );

            // Success Notification
            $notifMsg = 'Pembayaran Tunai Berhasil: Rp' . number_format($totalAmountToPay, 0, ',', '.');
            \Filament\Notifications\Notification::make()
                ->title($notifMsg)
                ->success()
                ->send();

            $this->finalizePayment($order, $payment, $totalAmountToPay, $isFullyPaid);

        } elseif ($this->paymentMethod === 'qris') {
            // Generate Midtrans Snap Token specific for QRIS
            try {
                $midtransOrderId = $order->id . '-SPLIT-' . time();

                // Create PENDING Payment first to lock the reference and items
                $pendingPayment = \App\Models\OrderPayment::create([
                    'order_id' => $order->id,
                    'amount' => $totalAmountToPay,
                    'payment_method' => 'qris',
                    'status' => 'pending',
                    'reference_number' => $midtransOrderId
                ]);

                // Link items if Split by Item
                if ($this->isSplitBill && $this->splitType === 'item') {
                    \App\Models\OrderItem::whereIn('id', $this->selectedSplitItems)
                        ->where('is_paid', false)
                        ->update(['order_payment_id' => $pendingPayment->id]);
                }
                
                $midtransService = new \App\Services\MidtransService(\Filament\Facades\Filament::getTenant());
                
                // create a temporary struct for MidtransService
                $tempOrder = clone $order;
                $tempOrder->id = $midtransOrderId;
                $tempOrder->total_amount = $totalAmountToPay; 
                
                $snapToken = $midtransService->createSnapToken($tempOrder, [
                    'enabled_payments' => ['qris', 'gopay', 'shopeepay'], // Force QRIS/E-Wallet
                ]);

                if ($snapToken) {
                    $this->dispatch('start-midtrans-payment', token: $snapToken, order_id: $order->id, amount: $totalAmountToPay);
                } else {
                    \Filament\Notifications\Notification::make()
                        ->title('Gagal Membuat QRIS')
                        ->body('Midtrans service failed.')
                        ->danger()
                        ->send();
                }
            } catch (\Exception $e) {
                \Filament\Notifications\Notification::make()
                    ->title('Error')
                    ->body($e->getMessage())
                    ->danger()
                    ->send();
            }
        } elseif ($this->paymentMethod === 'edc') {
            if (!auth()->user()?->hasFeature('EDC Integration')) {
                \Filament\Notifications\Notification::make()
                    ->title('Upgrade Paket')
                    ->body('Fitur Integrasi EDC tidak tersedia di paket Anda.')
                    ->danger()
                    ->send();
                return;
            }

            if (! (auth()->user()?->hasRole('restaurant_owner') || auth()->user()?->can('use_edc_payment'))) {
                \Filament\Notifications\Notification::make()
                    ->title('Akses Ditolak')
                    ->body('Anda tidak memiliki izin (permission) untuk memproses pembayaran via EDC.')
                    ->danger()
                    ->send();
                return;
            }

            if (empty($this->selectedBank)) {
                \Filament\Notifications\Notification::make()
                    ->title('Silakan pilih Bank EDC')
                    ->danger()
                    ->send();
                return;
            }
            if (empty($this->edcReference)) {
                \Filament\Notifications\Notification::make()
                    ->title('Trace No / Reference ID wajib diisi')
                    ->danger()
                    ->send();
                return;
            }

            [$isFullyPaid, $payment] = $checkoutAction->execute(
                $order,
                $totalAmountToPay,
                'edc',
                ($this->isSplitBill && $this->splitType === 'item') ? $this->selectedSplitItems : [],
                $this->edcReference,
                ['bank_name' => $this->selectedBank]
            );

            // Success Notification
            \Filament\Notifications\Notification::make()
                ->title('Pembayaran EDC ' . $this->selectedBank . ' Berhasil')
                ->success()
                ->send();

            $this->finalizePayment($order, $payment, $totalAmountToPay, $isFullyPaid);
        }
    }

    protected function finalizePayment($order, $payment, $amountPaid, $isFullyPaid)
    {
        // Send WhatsApp
        if ($isFullyPaid && $order->restaurant->wa_is_active && $this->customerPhone) {
            \App\Jobs\SendOrderWhatsAppMessage::dispatch($order, $this->customerPhone);
        }

        // Print Receipt
        $this->dispatch('open-receipt', url: route('order.print', $order) . '?payment_id=' . $payment->id);

        // Reset or Continue
        if ($isFullyPaid) {
            $this->cart = [];
            $this->selectedTableId = null;
            $this->customerName = ''; 
            $this->customerPhone = '';
            $this->existingOrderId = null;
            $this->isSplitBill = false;
            $this->splitType = 'amount';
            $this->selectedSplitItems = [];
            $this->splitAmount = 0;
            $this->paymentMethod = null;
            $this->cashReceived = 0;
            $this->selectedBank = null;
            $this->edcReference = '';
            $this->remainingBalance = 0;
            $this->voucherId = null;
            $this->voucherCode = '';
            $this->isMember = false;
            $this->member = null;
            $this->wantsToRegister = false;
            
            if ($order->discount_id) {
                \App\Models\Discount::find($order->discount_id)?->increment('total_usage');
            }

            // --- Gift Card Deduction ---
            if ($this->appliedGiftCard && $this->giftCardDiscount > 0) {
                $freshCard = \App\Models\GiftCard::lockForUpdate()->find($this->appliedGiftCard['id']);
                if ($freshCard && $freshCard->isUsable()) {
                    $freshCard->applyAmount($this->giftCardDiscount, $order->id);
                }
            }
            $this->appliedGiftCard = null;
            $this->giftCardCode = '';
            $this->giftCardError = '';
            $this->giftCardDiscountAmount = 0;
            // --- End Gift Card ---
            
        } else {
            // Update remaining
            $this->remainingBalance = max(0, $order->total_amount - $order->amount_paid);
            $this->splitAmount = $this->remainingBalance;
        }
    }

    public function handlePaymentSuccess($orderId, $amount = null)
    {
        $order = \App\Models\Order::find($orderId);
        if ($order) {
            $amount = $amount ?? $order->total_amount;

            // Memanggil logika utama (yang biasa dieksekusi oleh webhook Midtrans) 
            // agar Pemisahan Fee dan Ledger saldo RESTORAN tetap berjalan meski di environment lokal.
            try {
                $midtransOrderId   = $order->id . '-LOCAL-' . time(); // Kasih fake suffix biar terbaca unique reference_number
                $transactionStatus = 'settlement';
                $fraudStatus       = 'accept';
                $paymentType       = 'qris';
                $grossAmount       = (float) $amount;

                app(\App\Http\Controllers\MidtransController::class)->handleOrderPayment(
                    $midtransOrderId,
                    $transactionStatus,
                    $fraudStatus,
                    $paymentType,
                    $grossAmount
                );
            } catch (\Exception $e) {
                \Log::error('Local Midtrans Success Callback Error: ' . $e->getMessage());
            }

            $order->refresh();

            $isFullyPaid = $order->payment_status === 'paid';
            $payment     = $order->orderPayments()->latest()->first();

            \Filament\Notifications\Notification::make()
                ->title('Pembayaran QRIS Berhasil: Rp' . number_format($amount, 0, ',', '.'))
                ->success()
                ->send();

            $this->finalizePayment($order, $payment, $amount, $isFullyPaid);
        }
    }

    public function getTitle(): string
    {
        return 'Point of Sales';
    }

    public function updatedSelectedCategory()
    {
        // $this->resetPage(); // Not using pagination yet
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $tenant = \Filament\Facades\Filament::getTenant();

        // 1. Fetch Categories (Cached)
        $categories = \App\Models\MenuItem::getCachedCategories($tenant->id);

        // 2. Fetch Menu Items (Cached & Filtered in PHP)
        $menuItems = \App\Models\MenuItem::getCachedItems($tenant->id);

        if ($this->selectedCategory !== 'all') {
            $menuItems = $menuItems->where('menu_category_id', $this->selectedCategory);
        }

        if ($this->search) {
            $search = strtolower($this->search);
            $menuItems = $menuItems->filter(function($item) use ($search) {
                return str_contains(strtolower($item->name), $search);
            });
        }

        // 3. Fetch Tables
        $tables = \App\Models\Table::where('restaurant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // 4. Fetch Unpaid Orders
        $unpaidOrders = collect();
        if ($this->showOrders) {
            $unpaidOrders = \App\Models\Order::where('restaurant_id', $tenant->id)
                ->whereIn('payment_status', ['unpaid', 'partial', 'pending'])
                ->with(['items.menuItem', 'table'])
                ->withSum(['orderPayments' => fn($q) => $q->where('status', 'paid')], 'amount')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // 5. Check Active Register Session (Empire Feature)
        if (auth()->user()->hasFeature('Cash Drawer Integration')) {
            $this->activeSession = \App\Models\PosRegisterSession::where('restaurant_id', $tenant->id)
                ->where('status', 'open')
                ->first();
            
            if (!$this->activeSession && !$this->showRegisterModal) {
                $this->showRegisterModal = true;
            }
        }

        return view($this->getView(), [
            'categories' => $categories,
            'menuItems' => $menuItems,
            'tables' => $tables,
            'unpaidOrders' => $unpaidOrders,
            'restaurant' => $tenant,
            'activeSession' => $this->activeSession,
        ])
            ->layout(static::$layout, [
                'livewire' => $this,
                'maxContentWidth' => $this->getMaxContentWidth(),
                ...$this->getLayoutData(),
            ]);
    }
}
