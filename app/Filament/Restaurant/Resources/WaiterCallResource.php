<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\WaiterCallResource\Pages;
use App\Models\WaiterCall;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class WaiterCallResource extends Resource
{
    protected static ?string $model = WaiterCall::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    
    protected static ?string $navigationLabel = 'Panggilan Pelayan';
    
    protected static ?string $modelLabel = 'Panggilan Pelayan';
    
    protected static ?string $navigationGroup = 'OPERASIONAL';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return parent::canAccess() && auth()->user()->hasFeature('Waiter Call System');
    }
    
    // Show badge with pending count
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('restaurant_id', \Filament\Facades\Filament::getTenant()->id)
            ->where('status', 'pending')
            ->count() ?: null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('table_id')
                    ->relationship('table', 'name')
                    ->required()
                    ->disabled(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'responded' => 'Responded',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->disabled(),
                Forms\Components\DateTimePicker::make('called_at')
                    ->label('Called At')
                    ->disabled(),
                Forms\Components\DateTimePicker::make('responded_at')
                    ->label('Responded At')
                    ->disabled(),
                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('table.name')
                    ->label('Table')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('table.area')
                    ->label('Area')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'pending',
                        'success' => 'responded',
                        'secondary' => 'cancelled',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('called_at')
                    ->label('Called')
                    ->dateTime('H:i:s')
                    ->sortable()
                    ->description(fn (WaiterCall $record): string => $record->called_at->diffForHumans()),
                Tables\Columns\TextColumn::make('responder.name')
                    ->label('Responded By')
                    ->default('-')
                    ->sortable(),
            ])
            ->defaultSort('called_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'responded' => 'Responded',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('pending'),
            ])
            ->actions([
                Tables\Actions\Action::make('respond')
                    ->label('Respond')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (WaiterCall $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (WaiterCall $record) {
                        $record->markAsResponded();
                        
                        // Broadcast to customer
                        broadcast(new \App\Events\WaiterCallResponded($record));

                        Notification::make()
                            ->title('Call Responded')
                            ->body("Table {$record->table->name} has been attended.")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->poll('5s'); // Auto-refresh every 5 seconds
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
            'index' => Pages\ListWaiterCalls::route('/'),
            'view' => Pages\ViewWaiterCall::route('/{record}'),
        ];
    }
}
