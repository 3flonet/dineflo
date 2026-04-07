<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\OrderFeedbackResource\Pages;
use App\Filament\Restaurant\Resources\OrderFeedbackResource\RelationManagers;
use App\Models\OrderFeedback;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderFeedbackResource extends Resource
{
    protected static ?string $model = OrderFeedback::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    
    protected static ?string $navigationGroup = 'KATALOG & PROMO';
    
    protected static ?string $navigationLabel = 'Ulasan Pelanggan';

    protected static ?int $navigationSort = 4;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasFeature('Customer Feedback & Ratings');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }

    public static function canCreate(): bool
    {
        return false; // Feedback is created by customers
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Feedback Detail')
                    ->schema([
                        Forms\Components\Placeholder::make('order_number')
                            ->label('Order Number')
                            ->content(fn ($record) => $record->order->order_number),
                        
                        Forms\Components\Placeholder::make('rating')
                            ->label('Rating')
                            ->content(fn ($record) => str_repeat('⭐', $record->rating)),
                        
                        Forms\Components\Textarea::make('comment')
                            ->label('Customer Comment')
                            ->rows(3)
                            ->disabled(),
                        
                        Forms\Components\Toggle::make('is_public')
                            ->label('Tampilkan di Halaman Publik')
                            ->helperText('Jika aktif, testimoni ini akan muncul di website restoran Anda.')
                            ->onColor('success'),
                    ])->columns(2),

                Forms\Components\Section::make('Balas Tanggapan')
                    ->schema([
                        Forms\Components\Textarea::make('reply_comment')
                            ->label('Pesan Balasan Anda')
                            ->placeholder('Contoh: Terima kasih atas masukannya, akan kami perbaiki di masa depan.')
                            ->rows(3)
                            ->helperText('* Menekan tombol simpan akan mengirimkan balasan ini ke WhatsApp dan Email pelanggan secara otomatis.'),
                        
                        Forms\Components\Placeholder::make('replied_at')
                            ->label('Dibalas Pada')
                            ->content(fn ($record) => $record->replied_at?->format('d M Y H:i') ?? '-')
                            ->visible(fn ($record) => $record->replied_at !== null),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Order')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', $state))
                    ->color('warning'),
                
                Tables\Columns\TextColumn::make('comment')
                    ->label('Comment')
                    ->limit(50)
                    ->searchable(),
                
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Public')
                    ->boolean()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('replied_at')
                    ->label('Replied')
                    ->dateTime('d M Y')
                    ->placeholder('Draft')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->options([
                        '5' => '⭐⭐⭐⭐⭐',
                        '4' => '⭐⭐⭐⭐',
                        '3' => '⭐⭐⭐',
                        '2' => '⭐⭐',
                        '1' => '⭐',
                    ]),
                Tables\Filters\Filter::make('is_public')
                    ->query(fn (Builder $query): Builder => $query->where('is_public', true)),
                Tables\Filters\Filter::make('is_replied')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('replied_at')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->label('Reply')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->mutateRecordDataUsing(function (array $data): array {
                        $data['replied_at'] = now();
                        $data['replied_by'] = auth()->id();
                        return $data;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListOrderFeedback::route('/'),
            'view' => Pages\ViewOrderFeedback::route('/{record}'),
            'edit' => Pages\EditOrderFeedback::route('/{record}/reply'),
        ];
    }
}
