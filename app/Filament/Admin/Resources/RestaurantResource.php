<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RestaurantResource\Pages;
use App\Filament\Admin\Resources\RestaurantResource\RelationManagers;
use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RestaurantResource extends Resource
{
    use \App\Traits\TenancyScope;

    public static function getTenancyColumn(): string
    {
        return 'id';
    }
    protected static ?string $model = Restaurant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General Info')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('owner', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Owner'),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Contact & Address')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('zip_code')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Branding')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo Utama')
                            ->image()
                            ->disk('public')
                            ->directory('restaurants/logos')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->imagePreviewHeight('100')
                            ->downloadable()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp']),
                        Forms\Components\FileUpload::make('logo_square')
                            ->label('Logo Kotak (Opsional)')
                            ->image()
                            ->disk('public')
                            ->directory('restaurants/logos_square')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->imagePreviewHeight('100')
                            ->downloadable()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp']),
                        Forms\Components\FileUpload::make('cover_image')
                            ->image()
                            ->disk('public')
                            ->directory('restaurants/covers')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->imagePreviewHeight('100')
                            ->downloadable()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp']),
                    ])->columns(2),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: false),
                    
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Restaurant $record) => $record->owner?->name . ' • ' . $record->city),
                    
                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Owner')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: false),
                    
                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: false),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->toggleable(isToggledHiddenByDefault: false),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All Restaurants')
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only'),
                    
                Tables\Filters\SelectFilter::make('city')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('visit')
                    ->label('Visit')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Restaurant $record): string => route('restaurant.index', ['restaurant' => $record->slug]))
                    ->openUrlInNewTab(),
                \STS\FilamentImpersonate\Tables\Actions\Impersonate::make()
                    ->label('Login as Owner')
                    ->action(fn (Restaurant $record, \STS\FilamentImpersonate\Tables\Actions\Impersonate $action) => $action->impersonate($record->owner))
                    ->visible(fn (Restaurant $record) => $record->owner !== null && auth()->user()->canImpersonate() && $record->owner->canBeImpersonated())
                    ->redirectTo(fn (Restaurant $record) => route('filament.restaurant.pages.dashboard', ['tenant' => $record->slug])),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->defaultSort('name')
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
            'index' => Pages\ListRestaurants::route('/'),
            'create' => Pages\CreateRestaurant::route('/create'),
            'edit' => Pages\EditRestaurant::route('/{record}/edit'),
        ];
    }
}
