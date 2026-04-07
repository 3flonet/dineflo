<?php

namespace App\Filament\Admin\Widgets;

use App\Models\WithdrawRequest;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingWithdrawTableWidget extends BaseWidget
{
    protected static ?string $heading = '⚠️ Permintaan Withdraw Menunggu Tindakan';
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                WithdrawRequest::query()
                    ->whereIn('status', ['pending', 'approved'])
                    ->with('restaurant')
                    ->latest()
            )
            ->emptyStateHeading('Tidak ada withdraw yang perlu ditindaklanjuti')
            ->emptyStateDescription('Semua permintaan penarikan dana telah diproses.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->columns([
                Tables\Columns\TextColumn::make('withdraw_code')
                    ->label('Kode')
                    ->badge()
                    ->color('gray')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('restaurant.name')
                    ->label('Restoran')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('net_transfer_amount')
                    ->label('Diterima (Net)')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('bank_name')
                    ->label('Bank')
                    ->badge()
                    ->color('indigo'),

                Tables\Columns\TextColumn::make('account_number')
                    ->label('No. Rekening')
                    ->copyable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'     => 'warning',
                        'approved'    => 'info',
                        'transferred' => 'success',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending'     => 'Menunggu Persetujuan',
                        'approved'    => 'Disetujui — Belum Transfer',
                        'transferred' => 'Selesai',
                        default       => $state,
                    }),

                Tables\Columns\TextColumn::make('requested_at')
                    ->label('Diajukan')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('review')
                    ->label('Tinjau')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (WithdrawRequest $record) => route('filament.admin.resources.withdraw-requests.index'))
                    ->openUrlInNewTab(),
            ]);
    }
}
