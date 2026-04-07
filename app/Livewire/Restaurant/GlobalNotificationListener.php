<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Models\Order;
use Filament\Facades\Filament;

class GlobalNotificationListener extends Component
{
    public $restaurantId;
    public $hasWaiterCallFeature = false;
    public $restaurantSlug;
    public $userPreferences = [];

    public function mount()
    {
        $tenant = Filament::getTenant();
        
        if ($tenant) {
            $this->restaurantId = $tenant->id;
            $this->restaurantSlug = $tenant->slug;
            $owner = $tenant->owner;
            // Check feature on the owner of the restaurant
            $this->hasWaiterCallFeature = optional($owner)->hasFeature('Waiter Call System');

            // Set User Preferences
            $user = auth()->user();
            $this->userPreferences = $user->notification_preferences ?? [
                'order_new' => ['database', 'push', 'sound'],
                'waiter_call' => ['database', 'push', 'sound'],
                'reservation_new' => ['database', 'push'],
                'withdraw_status' => ['database', 'push'],
            ];

            // Check Subscription Expiry
            $this->checkSubscriptionExpiry($owner, $tenant);
        }
    }

    protected function checkSubscriptionExpiry($owner, $tenant)
    {
        if (!$owner) return;
        
        $sub = $owner->activeSubscription;
        if (!$sub) return;

        $settings = app(\App\Settings\GeneralSettings::class);
        $thresholdDays = $settings->subscription_expiry_warning_days ?? 7;
        
        $expiresAt = $sub->expires_at;
        $daysLeft = (int) now()->diffInDays($expiresAt, false);
        
        // Show notification if within threshold AND not on the subscription page AND not shown in this session
        if ($daysLeft >= 0 && $daysLeft <= $thresholdDays && !session()->has('subscription_warning_shown_at_' . $sub->id) && !request()->routeIs('filament.restaurant.pages.my-subscription')) {
             Notification::make()
                ->title('Masa Langganan Hampir Habis')
                ->body("Paket langganan Anda akan berakhir dalam **$daysLeft hari**. Segera lakukan perpanjangan untuk menghindari gangguan layanan.")
                ->warning()
                ->persistent()
                ->actions([
                    Action::make('renew')
                        ->label('Perpanjang Sekarang')
                        ->button()
                        ->icon('heroicon-o-arrow-path')
                        ->url(route('filament.restaurant.pages.my-subscription', ['tenant' => $tenant->slug])),
                ])
                ->send();
            
            session()->put('subscription_warning_shown_at_' . $sub->id, now()->timestamp);
        }
    }

    public function handleOrderCreated($data)
    {
        $id = $data['id'] ?? null;
        if (!$id) return;
        
        $order = Order::find($id);
        if (!$order) return;
        
        // Cek preferensi user untuk notifikasi lencana (database)
        if (in_array('database', $this->userPreferences['order_new'] ?? [])) {
            Notification::make()
                ->title('New Order #' . $order->order_number)
                ->body("Table: " . (optional($order->table)->name ?? 'Unknown') . " - Customer: " . $order->customer_name)
                ->success()
                ->duration(10000)
                ->actions([
                    Action::make('view')
                        ->button()
                        ->url(fn () => \App\Filament\Restaurant\Resources\OrderResource::getUrl('edit', ['record' => $order, 'tenant' => $this->restaurantSlug]))
                        ->markAsRead(),
                ])
                ->send();
        }
            
        $this->dispatch('refresh-orders'); 
    }

    public function handleWaiterCalled($data)
    {
        if (!$this->hasWaiterCallFeature) return;

        $table = $data['table_name'] ?? 'Unknown Table';
        $msg = $data['message'] ?? "Table $table is calling!";

        // Cek preferensi user untuk notifikasi lencana (database)
        if (in_array('database', $this->userPreferences['waiter_call'] ?? [])) {
            Notification::make()
                ->title('Waiter Called!')
                ->body($msg)
                ->danger()
                ->persistent()
                ->actions([
                    Action::make('respond')
                        ->button()
                        ->url('/restaurants/' . $this->restaurantSlug . '/waiter-calls')
                        ->markAsRead(),
                ])
                ->send();
        }
            
        $this->dispatch('refresh-waiter-calls');
    }

    public function render()
    {
        return view('livewire.restaurant.global-notification-listener');
    }
}
