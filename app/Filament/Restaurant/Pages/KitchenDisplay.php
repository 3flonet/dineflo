<?php

namespace App\Filament\Restaurant\Pages;

use App\Models\Order;
use Filament\Pages\Page;
use Filament\Facades\Filament;

class KitchenDisplay extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string $view = 'filament.restaurant.pages.kitchen-display';
    
    protected static ?string $navigationLabel = 'Kitchen Display';
    
    protected static ?string $slug = 'kitchen-display';
    
    protected static ?string $title = 'Kitchen Display System';
    
    // Use simple layout and override max-width via CSS
    protected static string $layout = 'filament-panels::components.layout.simple';

    // Disembunyikan dari sidebar — akses lewat Quick Launch
    protected static bool $shouldRegisterNavigation = false;
    
    public static function canAccess(): bool
    {
        // Hide menu jika user tidak punya fitur 'Kitchen Display System'
        return auth()->user()->can('page_KitchenDisplay') && auth()->user()->hasFeature('Kitchen Display System');
    }
    
    /**
     * Update order status
     */
    public function updateStatus($orderId, $status)
    {
        $tenant = Filament::getTenant();
        
        $order = Order::where('restaurant_id', $tenant->id)->find($orderId);
        
        if ($order) {
            $order->status = $status;
            $order->save();
        }
    }

    /**
     * Pass data to the view
     */
    protected function getViewData(): array
    {
        $tenant = Filament::getTenant();
        
        // Fetch active orders only (exclude completed/cancelled)
        // Fetch active orders only (exclude completed/cancelled)
        $orders = Order::where('restaurant_id', $tenant->id)
            ->where(function($query) {
                $query->where('payment_status', 'paid')
                      ->orWhereIn('status', ['confirmed', 'cooking', 'ready_to_serve']);
            })
            ->whereIn('status', ['pending', 'confirmed', 'cooking', 'ready_to_serve'])
            ->with(['items.menuItem', 'items.variant', 'table'])
            ->orderBy('created_at', 'asc') // Oldest first (FIFO)
            ->get();

        return [
            'orders' => $orders,
            'incomingOrders' => $orders->whereIn('status', ['pending', 'confirmed']),
            'cookingOrders' => $orders->where('status', 'cooking'),
            'readyOrders' => $orders->where('status', 'ready_to_serve'),
            'restaurant' => $tenant,
        ];
    }
}
