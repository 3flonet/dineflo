<?php

namespace App\Filament\Admin\Resources;

use App\Models\MenuItem;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // Hide from Navigation
    protected static bool $shouldRegisterNavigation = false;

    // Use specific labels to help Shield
    protected static ?string $modelLabel = 'Menu Item';
    protected static ?string $pluralModelLabel = 'Menu Items';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getPages(): array
    {
        return [];
    }
}
