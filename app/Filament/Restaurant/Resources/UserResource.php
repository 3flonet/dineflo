<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;
use Filament\Facades\Filament;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Staff';

    protected static ?string $pluralModelLabel = 'Staff';

    protected static ?string $navigationLabel = 'Tim & Karyawan';

    protected static ?string $navigationGroup = 'PENGATURAN TOKO';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        // Only show users that belong to this restaurant (tenant scope handles owner, but we might want to hide super admin or ourselves if needed)
        // Filament tenant scope should handle this automatically.
        // However, we want to exclude Super Admins from being listed here if they are not part of the restaurant.
        return parent::getEloquentQuery()->where('id', '!=', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Staff Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),
                        Forms\Components\Select::make('roles')
                            ->label('Roles')
                            ->multiple()
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(function () {
                                // Get current tenant ID
                                $tenantId = \Filament\Facades\Filament::getTenant()->id;
                                
                                // Return roles that belong to this tenant OR roles that are global (null tenant)
                                $roles = Role::where('restaurant_id', $tenantId)
                                    ->orWhereNull('restaurant_id')
                                    ->where('name', '!=', 'super_admin')
                                    ->get();

                                // Prioritize tenant-specific over global, removing duplicates
                                return $roles->sortByDesc('restaurant_id')
                                    ->unique('name')
                                    ->pluck('name', 'id');
                            })
                            // Load existing roles for this user in this tenant context
                            ->loadStateFromRelationshipsUsing(function (Forms\Components\Select $component, ?User $record) {
                                if (!$record) return;
                                
                                // Set team ID to filter roles for this tenant
                                setPermissionsTeamId(\Filament\Facades\Filament::getTenant()->id);
                                $record->load('roles'); 

                                $component->state($record->roles->pluck('id')->toArray());
                            })
                            // Custom save logic to ensure correct team ID is used
                            ->saveRelationshipsUsing(function (User $record, $state) {
                                // Set team ID before syncing
                                setPermissionsTeamId(\Filament\Facades\Filament::getTenant()->id);
                                
                                // Sync roles using Spatie's method which respects team ID
                                $roles = Role::whereIn('id', $state)->get();
                                $record->syncRoles($roles);
                            })
                            ->dehydrated(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'restaurant_owner' => 'warning',
                        'staff' => 'info',
                        'kitchen' => 'success',
                        'waiter' => 'primary',
                        default => 'gray',
                    }),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
