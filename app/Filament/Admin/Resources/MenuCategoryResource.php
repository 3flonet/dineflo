<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MenuCategoryResource\Pages;
use App\Models\MenuCategory;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class MenuCategoryResource extends Resource
{
    protected static ?string $model = MenuCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // IMPORTANT: Hide from Admin Navigation
    protected static bool $shouldRegisterNavigation = false;

    // Use specific model label to help Shield configure permissions
    protected static ?string $modelLabel = 'Menu Category';
    protected static ?string $pluralModelLabel = 'Menu Categories';
    
    // Set permission identifier explicitly to match Restaurant panel
    protected static ?string $slug = 'menu-categories'; 

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }


}
