<?php

namespace App\Filament\Admin\Resources;

use App\Models\Table; // Make sure this model exists
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table as FilamentTable;

class TableResource extends Resource
{
    protected static ?string $model = \App\Models\Table::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    // Hide from Navigation
    protected static bool $shouldRegisterNavigation = false;

    // Use specific labels to help Shield
    protected static ?string $modelLabel = 'Table';
    protected static ?string $pluralModelLabel = 'Tables';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(FilamentTable $table): FilamentTable
    {
        return $table->columns([]);
    }

    public static function getPages(): array
    {
        return [];
    }
}
