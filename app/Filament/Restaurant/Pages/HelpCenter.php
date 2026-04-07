<?php

namespace App\Filament\Restaurant\Pages;

use Filament\Pages\Page;

class HelpCenter extends Page implements \Filament\Tables\Contracts\HasTable
{
    use \Filament\Tables\Concerns\InteractsWithTable;
    use \Filament\Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-lifebuoy';
    protected static ?string $navigationLabel = 'Bantuan & Tiket';
    protected static ?string $title = 'Pusat Bantuan';
    protected static string $view = 'filament.restaurant.pages.help-center';

    public static function canAccess(): bool
    {
        return auth()->user()->can('page_HelpCenter') || auth()->user()->hasRole('restaurant_owner');
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('create_ticket')
                ->label('Buat Tiket Baru')
                ->icon('heroicon-o-plus')
                ->form([
                    \Filament\Forms\Components\TextInput::make('subject')
                        ->label('Subjek Kendala')
                        ->required()
                        ->maxLength(255),
                    \Filament\Forms\Components\Textarea::make('description')
                        ->label('Detail Kendala')
                        ->required()
                        ->rows(4),
                    \Filament\Forms\Components\FileUpload::make('attachment')
                        ->label('Lampiran File (Opsional)')
                        ->directory('support-attachments')
                        ->maxSize(5120)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf']),
                ])
                ->action(function (array $data) {
                    $user = auth()->user();
                    $restaurant = \Filament\Facades\Filament::getTenant();

                    // Priority handling
                    $priority = 'normal';
                    if ($user->hasRole('super_admin') || $user->hasFeature('Priority Support')) {
                        $priority = 'high';
                    }

                    \App\Models\SupportTicket::create([
                        'restaurant_id' => $restaurant->id,
                        'user_id' => $user->id,
                        'ticket_number' => 'TKT-' . date('Ymd') . '-' . strtoupper(str()->random(5)),
                        'subject' => $data['subject'],
                        'description' => $data['description'],
                        'attachment' => $data['attachment'] ?? null,
                        'status' => 'open',
                        'priority' => $priority,
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Tiket Berhasil Dibuat')
                        ->body('Tim support kami akan segera merespon tiket Anda.')
                        ->success()
                        ->send();
                })
        ];
    }

    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        $restaurant = \Filament\Facades\Filament::getTenant();

        return $table
            ->query(\App\Models\SupportTicket::where('restaurant_id', $restaurant?->id ?? 0)->latest())
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('ticket_number')
                    ->label('ID Tiket')
                    ->searchable()
                    ->weight('bold')
                    ->color('primary'),
                \Filament\Tables\Columns\TextColumn::make('subject')
                    ->label('Subjek')
                    ->limit(40)
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'danger',
                        'in_progress' => 'warning',
                        'resolved' => 'success',
                        'closed' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                \Filament\Tables\Columns\TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'normal' => 'info',
                        'high' => 'warning',
                        'urgent' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('view')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading('Detail Tiket')
                    ->modalWidth('xl')
                    ->infolist([
                        \Filament\Infolists\Components\Section::make('Informasi Tiket')
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('ticket_number')
                                    ->label('ID Tiket')
                                    ->copyable()
                                    ->copyMessage('ID Tiket disalin!'),
                                \Filament\Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'open' => 'danger',
                                        'in_progress' => 'warning',
                                        'resolved' => 'success',
                                        'closed' => 'gray',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                                \Filament\Infolists\Components\TextEntry::make('priority')
                                    ->label('Prioritas')
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'low' => 'gray',
                                        'normal' => 'info',
                                        'high' => 'warning',
                                        'urgent' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                \Filament\Infolists\Components\TextEntry::make('created_at')
                                    ->label('Tanggal Laporan')
                                    ->dateTime('d M Y, H:i'),
                            ])->columns(2),

                        \Filament\Infolists\Components\Section::make('Detail Keluhan')
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('subject')
                                    ->label('Subjek')
                                    ->weight('bold')
                                    ->columnSpanFull(),
                                \Filament\Infolists\Components\TextEntry::make('description')
                                    ->label('Deskripsi')
                                    ->markdown()
                                    ->prose()
                                    ->columnSpanFull(),
                            ]),

                        \Filament\Infolists\Components\Section::make('Lampiran / Dokumen Pendukung')
                            ->schema([
                                \Filament\Infolists\Components\ImageEntry::make('attachment')
                                    ->label('')
                                    ->hidden(fn ($record) => !$record->attachment || !in_array(pathinfo($record->attachment, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                    ->extraImgAttributes([
                                        'class' => 'rounded-xl shadow-sm ring-1 ring-gray-200 dark:ring-white/10 max-w-full h-auto object-cover',
                                        'style' => 'max-height: 400px;'
                                    ])
                                    ->columnSpanFull(),
                                
                                \Filament\Infolists\Components\TextEntry::make('attachment')
                                    ->label('File Dokumen (PDF)')
                                    ->hidden(fn ($record) => !$record->attachment || pathinfo($record->attachment, PATHINFO_EXTENSION) !== 'pdf')
                                    ->formatStateUsing(fn ($state) => '📄 Lihat / Unduh Dokumen PDF')
                                    ->color('primary')
                                    ->weight('bold')
                                    ->url(fn ($record) => \Illuminate\Support\Facades\Storage::url($record->attachment), true)
                                    ->columnSpanFull(),
                            ])
                            ->visible(fn ($record) => !empty($record->attachment))
                            ->collapsible(),
                    ])
                    ->modalSubmitAction(false),
            ]);
    }
}
