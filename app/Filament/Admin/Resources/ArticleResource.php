<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ArticleResource\Pages;
use App\Filament\Admin\Resources\ArticleResource\RelationManagers;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ArticleResource extends Resource
{
    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationLabel = 'News & Insights';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Section::make('Main Content')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state))),
                                
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->unique(Article::class, 'slug', ignoreRecord: true),

                                Forms\Components\Textarea::make('excerpt')
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Forms\Components\RichEditor::make('content')
                                    ->label('Article Content / Narrative')
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(2),

                        Forms\Components\Section::make('Metadata & Source')
                            ->schema([
                                Forms\Components\ToggleButtons::make('thumbnail_source')
                                    ->label('Thumbnail Source')
                                    ->options([
                                        'upload' => 'Upload File',
                                        'url' => 'Image Link',
                                    ])
                                    ->icons([
                                        'upload' => 'heroicon-o-cloud-arrow-up',
                                        'url' => 'heroicon-o-link',
                                    ])
                                    ->default('upload')
                                    ->live()
                                    ->dehydrated(false)
                                    ->afterStateHydrated(function (Forms\Components\ToggleButtons $component, $record) {
                                        if ($record && $record->thumbnail_url && !$record->thumbnail) {
                                            $component->state('url');
                                        }
                                    }),

                                Forms\Components\FileUpload::make('thumbnail')
                                    ->image()
                                    ->directory('articles')
                                    ->visible(fn (Forms\Get $get) => $get('thumbnail_source') === 'upload'),

                                Forms\Components\TextInput::make('thumbnail_url')
                                    ->label('External Image URL')
                                    ->url()
                                    ->placeholder('https://example.com/image.jpg')
                                    ->visible(fn (Forms\Get $get) => $get('thumbnail_source') === 'url'),

                                Forms\Components\Select::make('type')
                                    ->options([
                                        'internal' => 'Internal Report',
                                        'external' => 'Media Coverage',
                                    ])
                                    ->live()
                                    ->default('internal')
                                    ->required(),

                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('source_name')
                                        ->label('Media Name')
                                        ->placeholder('e.g. Detik.com')
                                        ->required(),
                                    Forms\Components\TextInput::make('source_url')
                                        ->label('Original Link')
                                        ->url()
                                        ->required(),
                                ])->visible(fn (Forms\Get $get) => $get('type') === 'external'),

                                Forms\Components\DateTimePicker::make('published_at')
                                    ->default(now()),

                                Forms\Components\Toggle::make('is_published')
                                    ->label('Published')
                                    ->default(true),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'internal' => 'success',
                        'external' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('source_name')
                    ->label('Media')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->label('Pub'),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'internal' => 'Internal',
                        'external' => 'External',
                    ]),
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
            ->defaultSort('published_at', 'desc');
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
