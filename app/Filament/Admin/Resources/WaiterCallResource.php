<?php

namespace App\Filament\Admin\Resources;

use App\Models\WaiterCall;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;

class WaiterCallResource extends Resource
{
    protected static ?string $model = WaiterCall::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Waiter Call';
    protected static ?string $pluralModelLabel = 'Waiter Calls';

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
