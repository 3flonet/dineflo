<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SocialPostResource\Pages;
use App\Filament\Admin\Resources\SocialPostResource\RelationManagers;
use App\Models\SocialPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SocialPostResource extends Resource
{
    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationIcon = 'heroicon-o-camera';

    protected static ?string $navigationLabel = 'Social Wall';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Post Configuration')
                    ->description('Curate your best social media moments here.')
                    ->schema([
                        Forms\Components\Select::make('platform')
                            ->options([
                                'instagram' => 'Instagram',
                            ])
                            ->default('instagram')
                            ->required(),
                        Forms\Components\Textarea::make('embed_code')
                            ->label('Embed Code (Paste from Instagram)')
                            ->placeholder('Go to Instagram post > ... > Embed > Copy Embed Code')
                            ->rows(8)
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('caption')
                            ->label('Label / Private Caption')
                            ->placeholder('Used for internal reference or alt text')
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Show on Community Wall')
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
                Tables\Columns\TextColumn::make('platform')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('caption')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Public'),
                Tables\Columns\TextColumn::make('sort_order')
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
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
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
            'index' => Pages\ListSocialPosts::route('/'),
            'create' => Pages\CreateSocialPost::route('/create'),
            'edit' => Pages\EditSocialPost::route('/{record}/edit'),
        ];
    }
}
