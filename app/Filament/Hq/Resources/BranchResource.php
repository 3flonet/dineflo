<?php

namespace App\Filament\Hq\Resources;

use App\Filament\Hq\Resources\BranchResource\Pages;
use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BranchResource extends Resource
{
    protected static ?string $model = Restaurant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    
    protected static ?string $navigationLabel = 'Manajemen Cabang';
    
    protected static ?string $modelLabel = 'Cabang';
    
    protected static ?string $pluralModelLabel = 'Daftar Cabang';

    protected static ?string $slug = 'branches';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if ($user->hasRole('super_admin')) return true;

        $sub = $user->activeSubscription;
        return $user->hasRole('restaurant_owner') && $sub && $sub->isValid() && $user->hasFeature('Multi-Restaurant Support');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasFeature('Multi-Restaurant Support') && auth()->user()->canCreateRestaurant();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->description('Detail utama identitas cabang Anda.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Cabang')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug / URL')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo Cabang')
                            ->image()
                            ->directory('restaurant-logos'),
                    ])->columns(2),

                Forms\Components\Section::make('Kontak & Lokasi')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Status Operasional')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif / Buka')
                            ->helperText('Jika dinonaktifkan, pelanggan tidak dapat melakukan pemesanan di cabang ini.')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Cabang')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Restaurant $record): string => $record->slug),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable()
                    ->action(function($record) {
                        $record->update(['is_active' => !$record->is_active]);
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar Sejak')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('visit')
                    ->label('Buka Panel')
                    ->icon('heroicon-m-arrow-right-circle')
                    ->color('info')
                    ->url(fn (Restaurant $record): string => "/restaurants/{$record->slug}")
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        
        // Bypass global tenant scope because we want to see ALL branches in HQ panel
        $query = parent::getEloquentQuery()->withoutGlobalScope('tenant');

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        return $query->where('user_id', $user->id);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
}
