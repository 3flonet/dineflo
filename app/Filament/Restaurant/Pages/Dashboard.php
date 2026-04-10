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

    /**
     * Override widgets to follow user's custom order.
     */
    public function getVisibleWidgets(): array
    {
        $widgets = parent::getVisibleWidgets();
        $settings = auth()->user()->settings;
        $customOrder = $settings['dashboard_widgets_order'] ?? null;

        if (!$customOrder || !is_array($customOrder)) {
            return $widgets;
        }

        // Sort widgets based on the stored order of class names
        return collect($widgets)
            ->sortBy(function ($widget) use ($customOrder) {
                $index = array_search($widget, $customOrder);
                return $index === false ? 999 : $index;
            })
            ->values()
            ->toArray();
    }

    /**
     * Livewire action to update widget order.
     */
    public function updateWidgetOrder(array $order): void
    {
        $user = auth()->user();
        $settings = $user->settings ?? [];
        $settings['dashboard_widgets_order'] = $order;
        
        $user->update(['settings' => $settings]);
        
        \Filament\Notifications\Notification::make()
            ->title('Urutan dashboard disimpan')
            ->success()
            ->send();
    }
}
