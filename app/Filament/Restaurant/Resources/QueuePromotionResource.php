<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\QueuePromotionResource\Pages;
use App\Filament\Restaurant\Resources\QueuePromotionResource\RelationManagers;
use App\Models\QueuePromotion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QueuePromotionResource extends Resource
{
    protected static ?string $model = QueuePromotion::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationLabel = 'Konten Promo Antrean';
    protected static ?string $modelLabel = 'Promo Antrean';
    protected static ?string $pluralModelLabel = 'Konten Promo Antrean';
    protected static ?string $navigationGroup = 'KIOS & ANTREAN';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Konten')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Konten (Internal)')
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->label('Tipe Konten')
                            ->options([
                                'image' => 'Gambar (JPG/PNG/WebP)',
                                'video' => 'Video (MP4/MOV/WEBM)',
                            ])
                            ->required()
                            ->live(),
                        Forms\Components\FileUpload::make('file_path')
                            ->label('File Asset')
                            ->required()
                            ->disk('public')
                            ->directory('queue-promotions')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/*', 'video/*'])
                            ->maxSize(1024000) // 1GB
                            ->preserveFilenames(),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('duration')
                                    ->label('Durasi Tampil (Detik)')
                                    ->required()
                                    ->numeric()
                                    ->default(10)
                                    ->helperText('Hanya berlaku untuk Gambar. Video akan diputar sampai habis.'),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Urutan Tampil')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true)
                                    ->inline(false),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('file_path')
                    ->label('Preview')
                    ->square()
                    ->placeholder('🎞️ VIDEO')
                    ->state(fn ($record) => $record->type === 'image' ? $record->file_path : null),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'image' => 'info',
                        'video' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => strtoupper($state)),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Durasi')
                    ->suffix(' detik')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListQueuePromotions::route('/'),
            'create' => Pages\CreateQueuePromotion::route('/create'),
            'edit' => Pages\EditQueuePromotion::route('/{record}/edit'),
        ];
    }
}
