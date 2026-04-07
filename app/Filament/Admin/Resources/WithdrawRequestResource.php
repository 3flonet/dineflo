<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WithdrawRequestResource\Pages;
use App\Models\WithdrawRequest;
use App\Models\RestaurantBalanceLedger;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Filament\Support\RawJs;

class WithdrawRequestResource extends Resource
{
    protected static ?string $model = WithdrawRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $navigationLabel = 'Withdraw Requests';
    protected static ?string $modelLabel = 'Withdraw Request';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Permintaan')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('restaurant_id')
                        ->label('Restoran')
                        ->relationship('restaurant', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->disabled(),

                    Forms\Components\TextInput::make('amount')
                        ->label('Jumlah (Rp)')
                        ->prefix('Rp')
                        ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                        ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                        ->required()
                        ->disabled(),

                    Forms\Components\TextInput::make('bank_name')
                        ->label('Nama Bank')
                        ->disabled(),

                    Forms\Components\TextInput::make('account_number')
                        ->label('Nomor Rekening')
                        ->disabled(),

                    Forms\Components\TextInput::make('account_name')
                        ->label('Nama Pemilik Rekening')
                        ->disabled(),
                ]),

            Forms\Components\Section::make('Status & Pemrosesan')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options(WithdrawRequest::statusOptions())
                        ->required()
                        ->live(),

                    Forms\Components\Placeholder::make('processed_by_name')
                        ->label('Diproses oleh')
                        ->content(fn ($record) => $record?->processedBy?->name ?? '-'),

                    Forms\Components\Textarea::make('notes')
                        ->label('Catatan / Referensi Transfer')
                        ->placeholder('Isi nomor referensi transfer, atau catatan lainnya...')
                        ->rows(3),

                    Forms\Components\FileUpload::make('transfer_receipt_path')
                        ->label('Bukti Transfer')
                        ->directory('withdraw-receipts')
                        ->image()
                        ->downloadable()
                        ->openable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('withdraw_code')
                    ->label('Kode Transaksi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('restaurant.name')
                    ->label('Restoran')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah Diminta')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('admin_fee_amount')
                    ->label('Admin Fee')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->admin_fee_percentage > 0
                            ? '- Rp ' . number_format($state, 0, ',', '.') . ' (' . $record->admin_fee_percentage . '%)'
                            : '-'
                    )
                    ->color('danger'),

                Tables\Columns\TextColumn::make('net_transfer_amount')
                    ->label('Net Transfer')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state ?? 0, 0, ',', '.'))
                    ->color('success')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('bank_name')
                    ->label('Bank'),

                Tables\Columns\TextColumn::make('account_number')
                    ->label('No. Rekening'),

                Tables\Columns\TextColumn::make('account_name')
                    ->label('Nama Pemilik'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors(WithdrawRequest::statusColors())
                    ->formatStateUsing(fn ($state) => WithdrawRequest::statusOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('requested_at')
                    ->label('Diminta Pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('transferred_at')
                    ->label('Ditransfer Pada')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(WithdrawRequest::statusOptions()),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-m-check-circle')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status'       => 'approved',
                            'approved_at'  => now(),
                            'processed_by' => Auth::id(),
                        ]);

                        // Kirim notifikasi ke pemilik restoran
                        $owner = $record->restaurant->owner;
                        if ($owner) {
                            Notification::make()
                                ->title('Withdraw Disetujui')
                                ->body("Permintaan withdraw Rp " . number_format($record->amount, 0, ',', '.') . " Anda telah disetujui dan sedang dalam proses transfer.")
                                ->success()
                                ->sendToDatabase($owner);
                        }

                        Notification::make()->title('Withdraw disetujui')->success()->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(2),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status'       => 'rejected',
                            'notes'        => $data['notes'],
                            'processed_by' => Auth::id(),
                        ]);

                        // Kirim notifikasi ke pemilik restoran
                        $owner = $record->restaurant->owner;
                        if ($owner) {
                            Notification::make()
                                ->title('Withdraw Ditolak')
                                ->body("Permintaan withdraw Rp " . number_format($record->amount, 0, ',', '.') . " Anda ditolak. Alasan: " . $data['notes'])
                                ->danger()
                                ->sendToDatabase($owner);
                        }

                        Notification::make()->title('Withdraw ditolak')->danger()->send();
                    }),

                Tables\Actions\Action::make('mark_transferred')
                    ->label('Konfirmasi Transfer')
                    ->icon('heroicon-m-paper-airplane')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'approved')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan / No. Referensi Transfer')
                            ->required()
                            ->rows(2),
                            
                        Forms\Components\FileUpload::make('transfer_receipt_path')
                            ->label('Bukti Transfer (Opsional)')
                            ->directory('withdraw-receipts')
                            ->image(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status'                => 'transferred',
                            'notes'                 => $data['notes'],
                            'transfer_receipt_path' => $data['transfer_receipt_path'] ?? null,
                            'transferred_at'        => now(),
                            'processed_by'          => Auth::id(),
                        ]);

                        // Kurangi saldo restoran
                        $record->restaurant->decrement('balance', $record->amount);

                        // Catat ke ledger sebagai debit
                        RestaurantBalanceLedger::create([
                            'restaurant_id'       => $record->restaurant_id,
                            'withdraw_request_id' => $record->id,
                            'type'                => 'debit',
                            'payment_type'        => null,
                            'gross_amount'        => $record->amount,
                            'fee_percentage'      => $record->admin_fee_percentage,
                            'fee_amount'          => $record->admin_fee_amount,
                            'gateway_fee_amount'  => 0,
                            'platform_fee_amount' => $record->admin_fee_amount,
                            'net_amount'          => $record->amount,
                            'description'         => 'Withdraw — Transfer ke ' . $record->bank_name . ' ' . $record->account_number . ' a/n ' . $record->account_name,
                        ]);

                        // Kirim notifikasi ke pemilik restoran
                        $owner = $record->restaurant->owner;
                        if ($owner) {
                            Notification::make()
                                ->title('Dana Withdraw Telah Ditransfer')
                                ->body("Withdraw Rp " . number_format($record->amount, 0, ',', '.') . " berhasil ditransfer ke " . $record->bank_name . " " . $record->account_number . ". Silakan cek rekening Anda.")
                                ->success()
                                ->sendToDatabase($owner);
                        }

                        Notification::make()->title('Transfer dikonfirmasi. Saldo restoran diperbarui.')->success()->send();
                    }),

                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make()
                        ->exports([
                            \pxlrbt\FilamentExcel\Exports\ExcelExport::make('export_withdraw_requests')->fromTable(),
                        ]),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWithdrawRequests::route('/'),
            'view'   => Pages\ViewWithdrawRequest::route('/{record}'),
        ];
    }
}
