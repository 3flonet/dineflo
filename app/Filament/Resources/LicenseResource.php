<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms;
use Filament\Actions;

class LicenseResource extends Resource
{
    protected static ?string $model = null;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'License';

    protected static ?string $title = 'License Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Settings';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('License Information')
                    ->schema([
                        Forms\Components\TextInput::make('license_key')
                            ->label('License Key')
                            ->default(env('LICENSE_KEY', 'Not configured'))
                            ->disabled()
                            ->copyable(),

                        Forms\Components\TextInput::make('license_status')
                            ->label('Status')
                            ->default(env('LICENSE_STATUS', 'inactive'))
                            ->disabled()
                            ->getStateUsing(fn() => \Illuminate\Support\Str::upper(env('LICENSE_STATUS', 'inactive')))
                            ->helperText('Current license status'),

                        Forms\Components\TextInput::make('license_domain')
                            ->label('Registered Domain')
                            ->default(env('LICENSE_DOMAIN', 'Not configured'))
                            ->disabled()
                            ->copyable(),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Customer Name')
                            ->default(env('LICENSE_CUSTOMER_NAME', 'Not configured'))
                            ->disabled(),

                        Forms\Components\TextInput::make('customer_email')
                            ->label('Customer Email')
                            ->default(env('LICENSE_CUSTOMER_EMAIL', 'Not configured'))
                            ->disabled()
                            ->copyable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('License Timeline')
                    ->schema([
                        Forms\Components\TextInput::make('license_last_ping_at')
                            ->label('Last Ping At')
                            ->default(env('LICENSE_LAST_PING_AT', 'Never'))
                            ->disabled()
                            ->helperText('Last time this installation pinged the license server'),

                        Forms\Components\TextInput::make('license_grace_until')
                            ->label('Grace Period Until')
                            ->default(env('LICENSE_GRACE_UNTIL', 'No grace period'))
                            ->disabled()
                            ->helperText('If empty, no grace period is active'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('LicenseHub Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('licensehub_api_url')
                            ->label('API URL')
                            ->default(env('LICENSEHUB_API_URL', 'http://licensehub.test'))
                            ->disabled()
                            ->copyable(),

                        Forms\Components\TextInput::make('licensehub_product_slug')
                            ->label('Product Slug')
                            ->default(env('LICENSEHUB_PRODUCT_SLUG', 'dineflo-pos'))
                            ->disabled()
                            ->copyable(),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\LicenseResource\Pages\ViewLicense::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }
}
