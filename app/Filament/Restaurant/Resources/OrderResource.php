<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\OrderResource\Pages;
use App\Filament\Restaurant\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\RawJs;
use App\Models\RefundLog;
use App\Models\RestaurantBalanceLedger;
use Filament\Notifications\Notification;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    protected static ?string $navigationLabel = 'Daftar Pesanan';

    protected static ?string $navigationGroup = 'OPERASIONAL';

    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pelanggan')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('order_number')
                                    ->label('Nomor Pesanan')
                                    ->disabled(),
                                Forms\Components\TextInput::make('customer_name')
                                    ->label('Nama Pelanggan')
                                    ->disabled(),
                                Forms\Components\TextInput::make('customer_phone')
                                    ->label('Telepon/WA')
                                    ->tel()
                                    ->disabled(),
                                Forms\Components\Select::make('table_id')
                                    ->label('Meja')
                                    ->relationship('table', 'name')
                                    ->placeholder('Takeaway')
                                    ->disabled(),
                                Forms\Components\TextInput::make('payment_method')
                                    ->label('Metode Bayar')
                                    ->disabled(),
                                Forms\Components\Select::make('payment_status')
                                    ->label('Status Bayar')
                                    ->options([
                                        'unpaid' => 'Belum Bayar',
                                        'paid' => 'Lunas',
                                        'partial' => 'Sebagian',
                                        'refunded' => 'Refund',
                                    ])
                                    ->disabled(),
                            ]),
                    ]),

                Forms\Components\Section::make('Status Operasional')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'cooking' => 'Cooking',
                                'ready_to_serve' => 'Ready to Serve',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->columnSpan(1),
                    ]),

                Forms\Components\Section::make('Rincian Pembayaran')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('subtotal')
                                    ->prefix('Rp')
                                    ->disabled(),
                                Forms\Components\TextInput::make('tax_amount')
                                    ->label('Pajak')
                                    ->prefix('Rp')
                                    ->disabled(),
                                Forms\Components\TextInput::make('additional_fees_amount')
                                    ->label('Biaya Tambahan')
                                    ->prefix('Rp')
                                    ->disabled(),
                                Forms\Components\TextInput::make('voucher_discount_amount')
                                    ->label('Potongan Voucher')
                                    ->prefix('- Rp')
                                    ->disabled(),
                                Forms\Components\TextInput::make('points_discount_amount')
                                    ->label('Potongan Poin')
                                    ->prefix('- Rp')
                                    ->disabled(),
                                Forms\Components\TextInput::make('gift_card_discount_amount')
                                    ->label('Potongan Gift Card')
                                    ->prefix('- Rp')
                                    ->disabled(),
                                Forms\Components\TextInput::make('total_amount')
                                    ->label('Total Akhir')
                                    ->prefix('Rp')
                                    ->extraInputAttributes(['class' => 'font-bold text-lg text-primary-600'])
                                    ->disabled(),
                            ]),
                    ]),
                
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan Pesanan')
                    ->disabled()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order #')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                    
                Tables\Columns\TextColumn::make('table.name')
                    ->label('Table/Type')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ?? 'Takeaway')
                    ->badge()
                    ->color(fn ($state) => $state ? 'gray' : 'warning')
                    ->toggleable(isToggledHiddenByDefault: false),
                    
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'pending',
                        'warning' => 'confirmed',
                        'info' => 'cooking',
                        'primary' => 'ready_to_serve',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'cooking' => 'Cooking',
                        'ready_to_serve' => 'Ready to Serve',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('pay_now')
                    ->label('Pay Now')
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->hidden(fn (Order $record) => $record->payment_status === 'paid' || $record->status === 'cancelled')
                    ->visible(fn () => auth()->user()->hasFeature('POS System'))
                    ->url(fn (Order $record) => \App\Filament\Restaurant\Pages\Pos::getUrl(['load_order' => $record->id], tenant: $record->restaurant)),
                Tables\Actions\Action::make('print')
                    ->label('Print Receipt')
                    ->icon('heroicon-o-printer')
                    ->url(fn (Order $record) => route('order.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('invoice')
                    ->label('Invoice PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn (Order $record) => route('order.download.invoice', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('send_whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->visible(fn (Order $record) => 
                        $record->restaurant->wa_is_active && 
                        ($record->customer_phone || $record->member?->phone) &&
                        auth()->user()->can('send_whatsapp_receipt')
                    )
                    ->form([
                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor WhatsApp')
                            ->tel()
                            ->default(fn (Order $record) => $record->customer_phone ?: $record->member?->phone)
                            ->required(),
                    ])
                    ->action(function (Order $record, array $data) {
                        \App\Jobs\SendOrderWhatsAppMessage::dispatch($record, $data['phone']);
                        
                        Notification::make()
                            ->title('Nota dalam antrian pengiriman')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('refund')
                    ->label('Refund')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->visible(fn (Order $record) => 
                        auth()->user()->hasFeature('Refund Handling') && 
                        auth()->user()->can('process_refunds') &&
                        in_array($record->payment_status, ['paid', 'partial']) &&
                        $record->status !== 'cancelled' &&
                        ($record->refund_status !== 'full')
                    )
                    ->modalHeading('Proses Pengembalian Dana (Refund)')
                    ->modalDescription('Pilih item yang ingin di-refund dan berikan alasan yang jelas.')
                    ->modalSubmitActionLabel('Proses Refund')
                    ->form([
                        Forms\Components\ViewField::make('refund_info')
                            ->view('filament.restaurant.resources.order.refund-info'),
                        Forms\Components\Repeater::make('refund_items')
                            ->label('Daftar Item untuk Refund')
                            ->schema([
                                Forms\Components\Select::make('order_item_id')
                                    ->label('Menu Item')
                                    ->options(function (Order $record, Forms\Get $get) {
                                        // Ambil semua ID yang sudah dipilih di baris lain dalam repeater
                                        $selectedIds = collect($get('../../refund_items'))
                                            ->pluck('order_item_id')
                                            ->filter()
                                            ->toArray();

                                        return $record->items()
                                            ->where('is_refunded', false)
                                            ->with('menuItem')
                                            ->get()
                                            ->filter(function ($item) use ($selectedIds, $get) {
                                                // Tampilkan item jika:
                                                // 1. Belum dipilih di baris manapun
                                                // 2. ATAU merupakan item yang sedang dipilih di baris AKTIF ini (agar tidak hilang saat terpilih)
                                                return !in_array($item->id, $selectedIds) || $item->id == $get('order_item_id');
                                            })
                                            ->pluck('menuItem.name', 'id');
                                    })
                                    ->required()
                                    ->live()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(), // Tambahan proteksi internal Filament
                                Forms\Components\Placeholder::make('item_price')
                                    ->label('Harga Item')
                                    ->content(fn ($get) => 
                                        $get('order_item_id') 
                                            ? 'Rp ' . number_format(\App\Models\OrderItem::find($get('order_item_id'))?->total_price ?? 0, 0, ',', '.')
                                            : '-'
                                    ),
                            ])
                            ->minItems(1)
                            ->addActionLabel('Tambah Item Refund'),
                        Forms\Components\Textarea::make('reason')
                            ->label('Alasan Refund')
                            ->required()
                            ->placeholder('Contoh: Menu habis / Pelanggan membatalkan'),
                        Forms\Components\Checkbox::make('restore_stock')
                            ->label('Kembalikan stok bahan baku?')
                            ->default(true)
                            ->helperText('Jika dicentang, sistem akan mencoba mengembalikan stok yang terpakai ke inventaris.'),
                    ])
                    ->action(function (Order $record, array $data): void {
                        \DB::transaction(function () use ($record, $data) {
                            $totalRefundAmount = 0;
                            $totalSubtotalRefunded = 0;
                            $refundedItemIds = [];
                            $restaurant = $record->restaurant;

                            foreach ($data['refund_items'] as $itemData) {
                                $orderItem = \App\Models\OrderItem::find($itemData['order_item_id']);
                                if ($orderItem && !$orderItem->is_refunded) {
                                    $orderItem->update([
                                        'is_refunded' => true,
                                        'refund_reason' => $data['reason']
                                    ]);
                                    
                                    $totalSubtotalRefunded += $orderItem->total_price;
                                    $refundedItemIds[] = $orderItem->id;

                                    // ── RESTORE STOCK LOGIC ───────────────────────────────────────
                                    if ($data['restore_stock']) {
                                        $menuItem = $orderItem->menuItem;
                                        if ($menuItem) {
                                            $ingredients = $menuItem->menuItemIngredients;
                                            foreach ($ingredients as $miIngredient) {
                                                $ingredient = $miIngredient->ingredient;
                                                if ($ingredient) {
                                                    $restoreQty = $miIngredient->quantity * $orderItem->quantity;
                                                    $ingredient->adjustStock(
                                                        $restoreQty, 
                                                        'in', 
                                                        'Refund Restore', 
                                                        $record, 
                                                        "Refund Item: {$menuItem->name} (Order #{$record->order_number})",
                                                        auth()->id()
                                                    );
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            if ($totalSubtotalRefunded > 0) {
                                // ── CALCULATE TAX & FEES PROPORTION ──────────────────────────
                                // Nominal refund = Subtotal + (Proporsi Pajak & Fee)
                                $originalSubtotal = $record->subtotal ?: $record->total_amount;
                                $refundRatio = $originalSubtotal > 0 ? ($totalSubtotalRefunded / $originalSubtotal) : 0;
                                
                                $taxRefund = ($record->tax_amount ?? 0) * $refundRatio;
                                $feeRefund = ($record->additional_fees_amount ?? 0) * $refundRatio;
                                
                                $totalRefundAmount = $totalSubtotalRefunded + $taxRefund + $feeRefund;

                                // ── CALCULATE isFullRefund LEBIH AWAL ────────────────────────
                                // Hitung sekali di sini, dipakai di loyalty, logging, dan update.
                                $isFullRefund = ($record->items()->where('is_refunded', false)->count() === 0);

                                // ── POINT REVERSAL LOGIC ──────────────────────────────────────
                                // Kurangi poin member secara proporsional sesuai nilai refund.
                                if ($record->is_loyalty_processed && $record->member) {
                                    $member = $record->member;
                                    $pointRate = $restaurant->loyalty_point_rate ?: 1000;
                                    $pointsToDeduct = floor($totalRefundAmount / $pointRate);
                                    
                                    if ($pointsToDeduct > 0) {
                                        $member->decrement('points_balance', $pointsToDeduct);
                                        $member->decrement('total_spent', $totalRefundAmount);
                                    }

                                    // ── FIX OBSERVER: Hanya reset flag saat FULL REFUND ──────
                                    // Ketika full refund, status akan berubah ke 'cancelled'.
                                    // Tanpa ini, OrderObserver::updated() akan memanggil
                                    // revertMemberLoyalty() dan memotong poin KEDUA KALI.
                                    // updateQuietly() membypass semua Observer agar aman.
                                    // Untuk partial refund, status tidak berubah ke 'cancelled'
                                    // sehingga Observer tidak akan memicu loyalty revert.
                                    if ($isFullRefund) {
                                        $record->updateQuietly(['is_loyalty_processed' => false]);
                                    }
                                }

                                // ── LOGGING & LEDGER ──────────────────────────────────────────
                                RefundLog::create([
                                    'restaurant_id' => $record->restaurant_id,
                                    'order_id'      => $record->id,
                                    'processed_by_id' => auth()->id(),
                                    'amount'        => $totalRefundAmount,
                                    'reason'        => $data['reason'],
                                    'refunded_items'=> $refundedItemIds,
                                    'is_full_refund'=> $isFullRefund,
                                ]);

                                // ── UPDATE ORDER STATE ───────────────────────────────────────
                                $newRefundedAmount = $record->refunded_amount + $totalRefundAmount;
                                
                                $record->update([
                                    'refunded_amount' => $newRefundedAmount,
                                    'refund_status'   => $isFullRefund ? 'full' : 'partial',
                                    'payment_status'  => $isFullRefund ? 'refunded' : $record->payment_status,
                                    'status'          => $isFullRefund ? 'cancelled' : $record->status,
                                ]);

                                // ── BALANCE LEDGER RECORD ────────────────────────────────────
                                RestaurantBalanceLedger::create([
                                    'restaurant_id' => $record->restaurant_id,
                                    'order_id'      => $record->id,
                                    'type'          => 'debit',
                                    'payment_type'  => $record->payment_method ?: 'cash',
                                    'gross_amount'  => $totalRefundAmount,
                                    'fee_amount'    => 0,
                                    'net_amount'    => $totalRefundAmount,
                                    'description'   => "Refund #{$record->order_number} ({$data['reason']})",
                                ]);

                                // Potong Saldo Restoran
                                $restaurant->decrement('balance', $totalRefundAmount);

                                Notification::make()
                                    ->title('Refund Berhasil Diarsip')
                                    ->body('Dana Rp ' . number_format($totalRefundAmount, 0, ',', '.') . ' dikembalikan (Inc. Pajak/Fee Proporsional). Poin member & stok disesuaikan.')
                                    ->success()
                                    ->send();

                                // Simpan data untuk dispatch notifikasi ke customer
                                // (dilakukan di LUAR transaction agar tidak rollback jika WA/email gagal)
                                $record->__refundAmountForDispatch = $totalRefundAmount;
                                $record->__isFullRefundForDispatch  = $isFullRefund;
                            }
                        });

                        // ── NOTIFIKASI CUSTOMER (di luar DB transaction) ──────────────────
                        $refundAmountToNotify = $record->__refundAmountForDispatch ?? null;
                        $isFullRefundToNotify = $record->__isFullRefundForDispatch ?? false;

                        if ($refundAmountToNotify !== null) {
                            $customerPhone = $record->customer_phone ?: $record->member?->phone;
                            $customerEmail = $record->customer_email;

                            // Kirim WA jika restoran aktif WA & customer punya nomor
                            if ($record->restaurant->wa_is_active && $customerPhone) {
                                \App\Jobs\SendRefundWhatsApp::dispatch(
                                    $record,
                                    $refundAmountToNotify,
                                    $data['reason'],
                                    $customerPhone
                                );
                            }

                            // Kirim email jika customer punya email
                            if ($customerEmail) {
                                \App\Jobs\SendWhitelabelMail::dispatch(
                                    $record->restaurant,
                                    $customerEmail,
                                    new \App\Mail\OrderRefunded(
                                        $record,
                                        $refundAmountToNotify,
                                        $data['reason'],
                                        $isFullRefundToNotify
                                    )
                                );
                            }
                        }
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->persistFiltersInSession()
            ->filtersFormColumns(1);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            // 'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
