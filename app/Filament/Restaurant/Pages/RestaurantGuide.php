<?php

namespace App\Filament\Restaurant\Pages;

use Filament\Pages\Page;

class RestaurantGuide extends Page
{
    protected static ?string $navigationIcon    = 'heroicon-o-book-open';
    protected static ?string $navigationLabel   = 'Panduan Operasional';
    protected static ?string $title             = 'Panduan Operasional Restoran';
    protected static ?string $navigationGroup   = null;
    protected static ?int    $navigationSort    = 99;
    protected static string  $view              = 'filament.restaurant.pages.restaurant-guide';

    /**
     * Hanya owner, manager, atau user dengan izin page_RestaurantGuide yang bisa akses.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user->hasRole(['restaurant_owner', 'manager', 'super_admin'])
            || $user->can('page_RestaurantGuide');
    }
}
