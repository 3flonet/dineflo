<?php

namespace App\Filament\Admin\Resources;

use App\Models\Member;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    // IMPORTANT: Hide from Admin Navigation
    protected static bool $shouldRegisterNavigation = false;

    // Use specific model label to help Shield configure permissions
    protected static ?string $modelLabel = 'Member';
    protected static ?string $pluralModelLabel = 'Members';
    
    // Set permission identifier explicitly to match Restaurant panel
    protected static ?string $slug = 'members'; 

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }
}
