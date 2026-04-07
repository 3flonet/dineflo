<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RoleResource\Pages;
use App\Filament\Concerns\HasResourceGroupedPermissions;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    use HasResourceGroupedPermissions;

    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon  = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Roles & Permissions';
    protected static ?string $navigationGroup = 'Access Control';
    protected static ?int    $navigationSort  = 0;

    /** Role sistem yang tidak boleh dihapus */
    public static array $systemRoles = ['super_admin', 'restaurant_owner'];

    /**
     * Kategori tab permissions — disesuaikan untuk cakupan Admin Panel
     * yang lebih luas (termasuk system broadcast, support ticket, dll.).
     */
    public static array $permissionCategories = [
        'operasional' => [
            'label'    => 'Operasional',
            'keywords' => ['order', 'table', 'waiter', 'member', 'reservation', 'pos', 'register', 'session', 'cash', 'drawer'],
            'icon'     => 'heroicon-m-shopping-cart',
        ],
        'menu' => [
            'label'    => 'Katalog & Menu',
            'keywords' => ['menu', 'gift', 'discount'],
            'icon'     => 'heroicon-m-book-open',
        ],
        'keuangan' => [
            'label'    => 'Keuangan & Stok',
            'keywords' => ['expense', 'ingredient', 'feedback', 'branch', 'withdraw'],
            'icon'     => 'heroicon-m-banknotes',
        ],
        'pemasaran' => [
            'label'    => 'Pemasaran',
            'keywords' => ['email', 'broadcast', 'campaign', 'whats', 'marketing'],
            'icon'     => 'heroicon-m-megaphone',
        ],
        'manajemen' => [
            'label'    => 'Manajemen Sistem',
            'keywords' => ['restaurant', 'user', 'role', 'support', 'ticket', 'system', 'subscription'],
            'icon'     => 'heroicon-m-building-office',
        ],
        'sistem' => [
            'label'    => 'Halaman & Widget',
            'keywords' => ['page_', 'widget_'],
            'icon'     => 'heroicon-m-cog-8-tooth',
        ],
        'khusus' => [
            'label'    => 'Khusus & Kasir',
            'keywords' => [],
            'icon'     => 'heroicon-m-key',
        ],
    ];

    // ─────────────────────────────────────────────────────────────
    // FORM
    // ─────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Role')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->disabled(fn (?Role $record) => $record && in_array($record->name, static::$systemRoles))
                                    ->helperText(fn (?Role $record) => $record && in_array($record->name, static::$systemRoles)
                                        ? '⚠️ Role sistem tidak dapat diubah namanya.'
                                        : ''),

                                Forms\Components\Select::make('guard_name')
                                    ->options(['web' => 'Web'])
                                    ->default('web')
                                    ->required()
                                    ->disabled(fn (?Role $record) => $record && in_array($record->name, static::$systemRoles)),

                                Forms\Components\Select::make('restaurant_id')
                                    ->label('Restaurant')
                                    ->relationship('restaurant', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Global Role')
                                    ->helperText('Kosongkan untuk role global sistem')
                                    ->disabled(fn (?Role $record) => $record && in_array($record->name, static::$systemRoles)),
                            ]),
                    ]),

                Forms\Components\Section::make('Hak Akses (Permissions)')
                    ->description('Tentukan izin untuk role ini. Setiap fitur dikelompokkan per resource agar mudah dibaca.')
                    ->schema([
                        Forms\Components\Tabs::make('PermissionsTabs')
                            ->tabs(function () {
                                $tabs = [];
                                foreach (static::$permissionCategories as $key => $config) {
                                    if ($key === 'sistem') {
                                        $schema = static::buildSistemSchema();
                                    } elseif ($key === 'khusus') {
                                        $schema = static::buildKhususSchema();
                                    } else {
                                        $schema = static::buildCategorySchema($config['keywords']);
                                    }

                                    if (!empty($schema)) {
                                        $tabs[] = Forms\Components\Tabs\Tab::make($config['label'])
                                            ->icon($config['icon'])
                                            ->schema($schema);
                                    }
                                }
                                return $tabs;
                            })
                            ->columnSpanFull(),
                    ])
                    ->footerActions([
                        Forms\Components\Actions\Action::make('reset')
                            ->label('Reset Semua Pilihan')
                            ->color('danger')
                            ->icon('heroicon-o-trash')
                            ->requiresConfirmation()
                            ->modalHeading('Reset semua permission?')
                            ->modalDescription('Semua centang akan dihapus. Anda masih perlu klik "Simpan" untuk menerapkan.')
                            ->action(function (Forms\Set $set) {
                                $allPerms = \Spatie\Permission\Models\Permission::all();
                                $keys     = collect();

                                foreach ($allPerms as $p) {
                                    [, $resource] = static::parsePermName($p->name);
                                    $keys->push(static::resourceKey($resource));
                                    $keys->push(static::resourceKey($resource) . '__adv');
                                }

                                $keys->push('res__pages', 'res__widgets', 'res__custom');

                                foreach ($keys->unique() as $key) {
                                    $set($key, []);
                                }
                            }),
                    ]),
            ]);
    }

    // ─────────────────────────────────────────────────────────────
    // TABLE
    // ─────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('restaurant.name')
                    ->label('Restaurant')
                    ->badge()
                    ->color('success')
                    ->default('Global')
                    ->formatStateUsing(fn ($state) => $state ?? 'Global')
                    ->searchable(),

                Tables\Columns\TextColumn::make('permissions.name')
                    ->label('Hak Akses Utama')
                    ->badge()
                    ->color('info')
                    ->separator(',')
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->formatStateUsing(function (string $state) {
                        [$action, $resource] = static::parsePermName($state);
                        $resLabel = ($resource !== '_custom' && !str_starts_with($resource, 'page_') && !str_starts_with($resource, 'widget_'))
                            ? static::resourceLabel($resource) . ': '
                            : '';
                        $actLabel = static::$actionLabels[$action] ?? str($action)->headline()->toString();
                        return $resLabel . $actLabel;
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Total')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('restaurant_id')
                    ->label('Restaurant')
                    ->relationship('restaurant', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('All Restaurants'),

                Tables\Filters\TernaryFilter::make('is_global')
                    ->label('Type')
                    ->placeholder('All Roles')
                    ->trueLabel('Global Only')
                    ->falseLabel('Tenant Only')
                    ->queries(
                        true:  fn ($query) => $query->whereNull('restaurant_id'),
                        false: fn ($query) => $query->whereNotNull('restaurant_id'),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (Role $record) => in_array($record->name, static::$systemRoles)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records
                                ->filter(fn ($r) => !in_array($r->name, static::$systemRoles))
                                ->each->delete();
                        }),
                ]),
            ])
            ->striped()
            ->defaultSort('name')
            ->persistFiltersInSession()
            ->filtersFormColumns(1);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit'   => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
