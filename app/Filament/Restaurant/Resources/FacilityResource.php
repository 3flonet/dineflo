<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\FacilityResource\Pages;
use App\Filament\Restaurant\Resources\FacilityResource\RelationManagers;
use App\Models\Facility;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FacilityResource extends Resource
{
    protected static ?string $model = Facility::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationLabel = 'Fasilitas & Galeri';

    protected static ?string $navigationGroup = 'PENGATURAN MINISITE';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Fasilitas')
                    ->description('Masukkan detail fasilitas yang tersedia di restoran Anda.')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Fasilitas')
                                    ->required()
                                    ->placeholder('Contoh: Playground Anak, Mushola, Ruang VIP')
                                    ->maxLength(255),
                                
                                Forms\Components\Select::make('icon')
                                    ->label('Ikon')
                                    ->options([
                                        'heroicon-o-sparkles' => 'Sparkles (Default)',
                                        'heroicon-o-home' => 'Home / Ruangan',
                                        'heroicon-o-wifi' => 'WiFi',
                                        'heroicon-o-academic-cap' => 'Playground / Edukasi',
                                        'heroicon-o-user-group' => 'VIP Room',
                                        'heroicon-o-sun' => 'Mushola / Ibadah',
                                        'heroicon-o-truck' => 'Parkir / Delivery',
                                        'heroicon-o-camera' => 'Galeri Foto',
                                    ])
                                    ->default('heroicon-o-sparkles'),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Singkat')
                            ->placeholder('Jelaskan sedikit tentang fasilitas ini...')
                            ->rows(3),
                        
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->default(0),
                    ]),

                Forms\Components\Section::make('Galeri Foto Fasilitas')
                    ->description('Unggah foto-foto yang menunjukkan keindahan fasilitas ini.')
                    ->schema([
                        Forms\Components\Repeater::make('photos')
                            ->relationship('photos')
                            ->schema([
                                Forms\Components\FileUpload::make('image_path')
                                    ->label('Foto')
                                    ->image()
                                    ->directory('facility-photos')
                                    ->imageEditor()
                                    ->required(),
                            ])
                            ->grid(2)
                            ->itemLabel(fn (array $state): ?string => 'Foto #' . ($state['id'] ?? 'Baru'))
                            ->addActionLabel('Tambah Foto ke Galeri')
                            ->collapsible(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Fasilitas')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('icon')
                    ->label('Ikon')
                    ->icon(fn (string $state): string => $state),

                Tables\Columns\TextColumn::make('photos_count')
                    ->label('Jumlah Foto')
                    ->counts('photos')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
            'index' => Pages\ListFacilities::route('/'),
            'create' => Pages\CreateFacility::route('/create'),
            'edit' => Pages\EditFacility::route('/{record}/edit'),
        ];
    }
}
