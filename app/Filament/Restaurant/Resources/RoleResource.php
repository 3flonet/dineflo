<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Concerns\HasResourceGroupedPermissions;
use App\Filament\Restaurant\Resources\RoleResource\Pages;
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
    protected static ?string $navigationLabel = 'Peran & Izin';
    protected static ?string $navigationGroup = 'PENGATURAN TOKO';
    protected static ?int    $navigationSort  = 3;

    /** Role sistem yang tidak boleh diedit/dihapus dari panel restoran */
    public static array $systemRoles = ['super_admin', 'restaurant_owner', 'restaurant_admin'];

    /** Kategori tab permission — konteks Restaurant Panel */
    public static array $permissionCategories = [
        'operasional' => [
            'label'    => 'Operasional',
            'keywords' => ['order', 'table', 'waiter', 'member', 'reservation', 'pos', 'register', 'session', 'cash', 'drawer', 'queue', 'promotion', 'edc'],
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
        'staf' => [
            'label'    => 'Staf & Akses',
            'keywords' => ['user', 'role', 'restaurant'],
            'icon'     => 'heroicon-m-users',
        ],
        'sistem' => [
            'label'    => 'Halaman & Widget',
            'keywords' => ['page_', 'widget_'],
            'icon'     => 'heroicon-m-cog-8-tooth',
        ],
        'khusus' => [
            'label'    => 'Khusus & Kasir',
            'keywords' => [], // kosong = semua custom permission (_custom)
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
                Forms\Components\Section::make('Informasi Role')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Role')
                            ->required()
                            ->disabled(fn (?Role $record) => $record && in_array($record->name, static::$systemRoles))
                            ->helperText(fn (?Role $record) => $record && in_array($record->name, static::$systemRoles)
                                ? '⚠️ Role sistem tidak dapat diubah namanya.'
                                : 'Nama role harus unik di restoran ini.'),
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
                    ->label('Role')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('permissions.name')
                    ->label('Hak Akses Utama')
                    ->badge()
                    ->color('info')
                    ->separator(',')
                    ->limitList(4)
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
                    ->label('Total Izin')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->striped()
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'edit'  => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
