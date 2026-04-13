<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\WeddingPackageResource\Pages;
use App\Filament\Restaurant\Resources\WeddingPackageResource\RelationManagers;
use App\Models\WeddingPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WeddingPackageResource extends Resource
{
    protected static ?string $model = WeddingPackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationLabel = 'Paket Wedding & Event';

    protected static ?string $navigationGroup = 'PENGATURAN MINISITE';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return parent::canAccess() && auth()->user()->hasFeature('Wedding & Event Packages');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar Paket')
                    ->description('Masukkan detail utama paket pernikahan atau event Anda.')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Paket')
                                    ->required()
                                    ->lazy()
                                    ->afterStateUpdated(fn ($set, $state) => $set('slug', \Illuminate\Support\Str::slug($state)))
                                    ->placeholder('Contoh: Elegant Intimate Wedding')
                                    ->maxLength(255),
                                
                                Forms\Components\TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->unique(WeddingPackage::class, 'slug', ignoreRecord: true),
                            ]),

                        Forms\Components\RichEditor::make('description')
                            ->label('Deskripsi Penawaran')
                            ->placeholder('Jelaskan detail paket, durasi, dan informasi penting lainnya...')
                            ->required()
                            ->columnSpanFull(),
                        
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('Harga Paket (Mulai Dari)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->placeholder('0'),
                                
                                Forms\Components\TextInput::make('min_capacity')
                                    ->label('Min. Kapasitas')
                                    ->numeric()
                                    ->suffix('Orang'),

                                Forms\Components\TextInput::make('max_capacity')
                                    ->label('Max. Kapasitas')
                                    ->numeric()
                                    ->suffix('Orang'),
                            ]),
                    ]),

                Forms\Components\Section::make('Visual & Media')
                    ->description('Unggah foto utama dan koleksi galeri untuk paket ini.')
                    ->schema([
                        Forms\Components\FileUpload::make('cover_image')
                            ->label('Foto Utama (Cover)')
                            ->image()
                            ->directory('wedding-covers')
                            ->imageEditor()
                            ->required(),
                        
                        Forms\Components\Repeater::make('gallery')
                            ->label('Galeri Foto Tambahan')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Foto')
                                    ->image()
                                    ->directory('wedding-galleries')
                                    ->imageEditor()
                                    ->required(),
                            ])
                            ->grid(3)
                            ->reorderable()
                            ->collapsible()
                            ->addActionLabel('Tambah Foto ke Galeri'),
                    ]),

                Forms\Components\Section::make('Rincian Paket (What\'s Included)')
                    ->description('Daftar apa saja yang didapat pelanggan dalam paket ini.')
                    ->schema([
                        Forms\Components\Repeater::make('inclusions')
                            ->label('Item Paket')
                            ->simple(
                                Forms\Components\TextInput::make('item')
                                    ->placeholder('Contoh: Catering untuk 100 pax')
                                    ->required(),
                            )
                            ->addActionLabel('Tambah Item Baru')
                            ->reorderable()
                            ->collapsible(),
                    ]),

                Forms\Components\Section::make('Pengaturan Tampilan')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Tampilkan Paket di Minisite')
                            ->default(true),
                        
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Cover')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Paket')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable()
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->getStateUsing(fn ($record) => $record->min_capacity . ' - ' . $record->max_capacity . ' org'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
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
            'index' => Pages\ListWeddingPackages::route('/'),
            'create' => Pages\CreateWeddingPackage::route('/create'),
            'edit' => Pages\EditWeddingPackage::route('/{record}/edit'),
        ];
    }
}
