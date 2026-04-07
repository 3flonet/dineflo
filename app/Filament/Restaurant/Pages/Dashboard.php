<?php

namespace App\Filament\Restaurant\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = -2;
    protected static string $routePath = '/';
    protected static string $view = 'filament.restaurant.pages.dashboard';
}
