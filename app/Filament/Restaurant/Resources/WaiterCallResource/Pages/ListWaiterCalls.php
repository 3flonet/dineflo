<?php

namespace App\Filament\Restaurant\Resources\WaiterCallResource\Pages;

use App\Filament\Restaurant\Resources\WaiterCallResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListWaiterCalls extends ListRecords
{
    protected static string $resource = WaiterCallResource::class;
    
    protected static string $view = 'filament.restaurant.resources.waiter-call-resource.pages.list-waiter-calls';

    protected function getHeaderActions(): array
    {
        return [
            // Remove create action - calls are created by customers
        ];
    }

    // Listen for real-time waiter call events
    protected function getListeners(): array
    {
        $restaurantId = \Filament\Facades\Filament::getTenant()->id;
        
        return [
            "echo-private:restaurant.{$restaurantId},.waiter.called" => 'handleWaiterCalled',
        ];
    }

    public function handleWaiterCalled($event): void
    {
        // Refresh the table to show new call
        $this->dispatch('$refresh');
        
        // Show notification
        Notification::make()
            ->title('New Waiter Call!')
            ->body($event['message'])
            ->icon('heroicon-o-bell-alert')
            ->iconColor('danger')
            ->duration(10000)
            ->send();
            
        // Play sound (optional - will add JS for this)
        $this->dispatch('play-notification-sound');
    }
}
