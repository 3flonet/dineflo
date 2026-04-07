<?php

namespace App\Filament\Restaurant\Pages;

use App\Models\WithdrawRequest;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\RawJs;

class WithdrawPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Penarikan Dana';

    protected function getSiteName(): string
    {
        try {
            return app(\App\Settings\GeneralSettings::class)->site_name;
        } catch (\Throwable $e) {
            return config('app.name', 'Dineflo');
        }
    }

    protected static ?string $title = 'Penarikan Dana (Withdraw)';
    protected static ?string $navigationGroup = 'KEUANGAN';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.restaurant.pages.withdraw-page';

    // Hanya tampil jika restoran menggunakan akun Dineflo DAN punya fitur Payment Gateway Withdraw
    public static function shouldRegisterNavigation(): bool
    {
        $tenant = Filament::getTenant();
        $user   = auth()->user();

        return $tenant
            && $tenant->gateway_mode === 'dineflo'
            && $user?->hasFeature('Payment Gateway Withdraw')
            && ($user?->can('view_withdraw_balance') || $user?->hasRole('restaurant_owner'));
    }

    public static function canAccess(): bool
    {
        $tenant = Filament::getTenant();
        $user   = auth()->user();

        if ($user?->hasRole('super_admin')) return true;

        return $tenant
            && $tenant->gateway_mode === 'dineflo'
            && $user?->hasFeature('Payment Gateway Withdraw')
            && ($user?->can('view_withdraw_balance') || $user?->hasRole('restaurant_owner'));
    }

    // Form untuk ajukan withdraw baru
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        $settings       = app(\App\Settings\GeneralSettings::class);
        $adminFeePct    = $settings->dineflo_withdraw_admin_fee_percentage ?? 0;
        
        $tenant = Filament::getTenant();
        $pendingAmount = \App\Models\WithdrawRequest::where('restaurant_id', $tenant?->id)
            ->whereIn('status', ['pending', 'approved'])
            ->sum('amount');
        $availableBalance = max(0, ($tenant?->balance ?? 0) - $pendingAmount);

        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah Penarikan (Rp)')
                            ->prefix('Rp')
                            ->required()
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->dehydrateStateUsing(fn ($state) => (float) str_replace('.', '', $state ?? 0))
                            ->helperText('Saldo bisa ditarik: Rp ' . number_format($availableBalance, 0, ',', '.') . ($pendingAmount > 0 ? " (Rp " . number_format($pendingAmount, 0, ',', '.') . " sedang diproses)" : ""))
                            ->live(debounce: 500),

                        Forms\Components\TextInput::make('bank_name')
                            ->label('Nama Bank')
                            ->placeholder('BCA, BNI, BRI, Mandiri, dll.')
                            ->required(),

                        Forms\Components\TextInput::make('account_number')
                            ->label('Nomor Rekening')
                            ->required(),

                        Forms\Components\TextInput::make('account_name')
                            ->label('Nama Pemilik Rekening')
                            ->required(),

                        // Preview breakdown fee jika admin fee aktif
                        Forms\Components\Placeholder::make('fee_breakdown')
                            ->label('Rincian Pembayaran')
                            ->columnSpanFull()
                            ->content(function (Forms\Get $get) use ($adminFeePct) {
                                $raw    = str_replace('.', '', $get('amount') ?? '0');
                                $amount = (float) $raw;
                                if ($amount <= 0) return 'Masukkan jumlah withdraw untuk melihat rincian.';

                                $adminFeeAmt  = round($amount * $adminFeePct / 100);
                                $netTransfer  = $amount - $adminFeeAmt;

                                if ($adminFeePct > 0) {
                                    return "💸 Jumlah withdraw: Rp " . number_format($amount, 0, ',', '.') .
                                           "\n✂️ Potongan admin " . $this->getSiteName() . " ({$adminFeePct}%): - Rp " . number_format($adminFeeAmt, 0, ',', '.') .
                                           "\n✅ Dana yang ditransfer ke rekening: Rp " . number_format($netTransfer, 0, ',', '.');
                                }

                                return "✅ Dana yang akan ditransfer: Rp " . number_format($amount, 0, ',', '.') . " (tidak ada potongan admin)";
                            }),

                        Forms\Components\Checkbox::make('agree_withdraw_terms')
                            ->label('Saya menyatakan bahwa data rekening yang dimasukkan sudah benar, dan saya menyetujui ketentuan penarikan dana termasuk potongan biaya admin yang berlaku. Pengajuan yang sudah dikirim tidak dapat dibatalkan.')
                            ->columnSpanFull()
                            ->accepted()
                            ->validationMessages([
                                'accepted' => 'Anda wajib menyetujui ketentuan penarikan dana sebelum mengajukan withdraw.',
                            ])
                            ->dehydrated(false)
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        // Cek permission
        if (! auth()->user()?->can('create_withdraw_request') && ! auth()->user()?->hasRole(['restaurant_owner', 'super_admin'])) {
            Notification::make()->title('Anda tidak memiliki akses untuk mengajukan withdraw.')->danger()->send();
            return;
        }

        $data     = $this->form->getState();
        $tenant   = Filament::getTenant();
        $settings = app(\App\Settings\GeneralSettings::class);
        
        $pendingAmount = \App\Models\WithdrawRequest::where('restaurant_id', $tenant->id)
            ->whereIn('status', ['pending', 'approved'])
            ->sum('amount');
        $availableBalance = max(0, $tenant->balance - $pendingAmount);

        if ((float) $data['amount'] > $availableBalance) {
            Notification::make()->title('Saldo tersedia tidak mencukupi. Anda mungkin masih memiliki penarikan yang sedang diproses.')->danger()->send();
            return;
        }

        // Hitung admin fee
        $adminFeePct = $settings->dineflo_withdraw_admin_fee_percentage ?? 0;
        $amount      = (float) $data['amount'];
        $adminFeeAmt = round($amount * $adminFeePct / 100);
        $netTransfer = $amount - $adminFeeAmt;

        WithdrawRequest::create([
            'restaurant_id'         => $tenant->id,
            'amount'                => $amount,
            'admin_fee_percentage'  => $adminFeePct,
            'admin_fee_amount'      => $adminFeeAmt,
            'net_transfer_amount'   => $netTransfer,
            'bank_name'             => $data['bank_name'],
            'account_number'        => $data['account_number'],
            'account_name'          => $data['account_name'],
            'status'                => 'pending',
            'requested_at'          => now(),
        ]);

        $this->form->fill();

        $body = $adminFeePct > 0
            ? "Dana yang akan ditransfer: Rp " . number_format($netTransfer, 0, ',', '.') . " (setelah potongan admin {$adminFeePct}%)."
            : "Tim " . $this->getSiteName() . " akan memproses transfer dalam 1-3 hari kerja.";

        Notification::make()
            ->title('Permintaan penarikan dana berhasil diajukan!')
            ->body($body)
            ->success()
            ->send();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\RestaurantBalanceLedger::where('restaurant_id', Filament::getTenant()?->id)
                    ->latest()
            )
            ->columns([
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipe')
                    ->colors([
                        'success' => 'credit',
                        'danger'  => 'debit',
                    ])
                    ->formatStateUsing(fn ($state) => $state === 'credit' ? '💰 Masuk' : '💸 Keluar'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan')
                    ->wrap(),

                Tables\Columns\TextColumn::make('transaction_code')
                    ->label('Kode Transaksi')
                    ->getStateUsing(function ($record) {
                        if ($record->type === 'credit' && $record->order_id) {
                            return $record->order->order_number ?? '-';
                        }
                        return $record->withdrawRequest->withdraw_code ?? '-';
                    })
                    ->searchable(query: function ($query, $search) {
                        return $query->whereHas('order', function ($q) use ($search) {
                            $q->where('order_number', 'like', "%{$search}%");
                        })->orWhereHas('withdrawRequest', function ($q) use ($search) {
                            $q->where('withdraw_code', 'like', "%{$search}%");
                        });
                    })
                    ->weight('bold')
                    ->copyable()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('gross_amount')
                    ->label('Gross')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                Tables\Columns\TextColumn::make('fee_amount')
                    ->label('Potongan Fee')
                    ->formatStateUsing(fn ($state, $record) =>
                        $state > 0
                            ? '- Rp ' . number_format($state, 0, ',', '.') .
                              ($record->fee_percentage > 0 ? ' (' . $record->fee_percentage . '%)' : '')
                            : '-'
                    )
                    ->color('danger'),

                Tables\Columns\TextColumn::make('net_amount')
                    ->label('Net (Saldo)')
                    ->formatStateUsing(fn ($state, $record) =>
                        ($record->type === 'credit' ? '+' : '-') .
                        ' Rp ' . number_format($state, 0, ',', '.')
                    )
                    ->color(fn ($record) => $record->type === 'credit' ? 'success' : 'danger')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status_display')
                    ->label('Status')
                    ->getStateUsing(fn () => 'Selesai')
                    ->colors(['success']),
            ])
            ->actions([
                Tables\Actions\Action::make('view_receipt')
                    ->label('Lihat Bukti')
                    ->icon('heroicon-o-photo')
                    ->color('info')
                    ->visible(fn ($record) => $record->type === 'debit' && $record->withdrawRequest?->transfer_receipt_path)
                    ->url(fn ($record) => \Illuminate\Support\Facades\Storage::url($record->withdrawRequest->transfer_receipt_path))
                    ->openUrlInNewTab(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe Transaksi')
                    ->options([
                        'credit' => 'Dana Masuk (Kredit)',
                        'debit'  => 'Withdraw/Keluar (Debit)',
                    ]),
            ])
            ->emptyStateHeading('Belum ada riwayat transaksi')
            ->emptyStateDescription('Riwayat kredit (pembayaran masuk) dan debit (withdraw) akan tampil di sini.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->exports([
                        \pxlrbt\FilamentExcel\Exports\ExcelExport::make('export_ledger')->fromTable(),
                    ])
                    ->icon('heroicon-o-document-arrow-down')
                    ->label('Export Riwayat (Excel)'),
            ])
            ->paginated([10, 25, 50]);
    }
}
