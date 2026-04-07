<?php

namespace App\Livewire\Kitchen;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;

class Board extends Component
{
    public Restaurant $restaurant;

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        
        if (auth()->check()) {
            setPermissionsTeamId($restaurant->id);
            auth()->user()->unsetRelation('roles');
            auth()->user()->unsetRelation('permissions');
        }

        // Basic Authorization Check
        // Ideally this should be done via middleware/policy, but checking here adds safety
        if (!auth()->check() || 
            (!auth()->user()->can('page_KitchenDisplay') && !auth()->user()->hasRole('restaurant_owner') && !auth()->user()->hasRole('super_admin'))) {
            abort(403, 'Unauthorized access to Kitchen Display System.');
        }

        // Feature Gating: Check if user has 'Kitchen Display System' feature
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasFeature('Kitchen Display System')) {
            abort(403, 'Anda memerlukan paket Pro untuk mengakses Kitchen Display System. Silakan upgrade paket Anda.');
        }
    }

    public function toggleItemReady($itemId)
    {
        $item = \App\Models\OrderItem::whereHas('order', function($q) {
            $q->where('restaurant_id', $this->restaurant->id);
        })->find($itemId);

        if ($item) {
            $item->update(['is_ready' => !$item->is_ready]);
        }
    }

    public function updateStatus($orderId, $newStatus)
    {
        $order = Order::where('restaurant_id', $this->restaurant->id)->find($orderId);
        
        if ($order) {
            // Check if moving to ready_to_serve, all items must be ready
            if ($newStatus === 'ready_to_serve') {
                $allReady = $order->items->every('is_ready', true);
                if (!$allReady) {
                    $this->dispatch('notify', [
                        'type' => 'error',
                        'message' => 'Semua menu harus dicentang sebelum dipindahkan ke Siap Saji.'
                    ]);
                    return;
                }
            }

            $updateData = ['status' => $newStatus];

            // ── ANALYTICS: RECORD TIMESTAMPS ──────────────────────────────
            if ($newStatus === 'cooking' && is_null($order->cooking_started_at)) {
                $updateData['cooking_started_at'] = now();
            }

            if (in_array($newStatus, ['ready_to_serve', 'completed']) && is_null($order->cooking_finished_at)) {
                $updateData['cooking_finished_at'] = now();
            }

            if ($newStatus === 'completed' && is_null($order->served_at)) {
                $updateData['served_at'] = now();
                $updateData['served_by_id'] = auth()->id();
            }

            $order->update($updateData);
        }
    }

    #[Layout('components.layouts.app', ['title' => 'Kitchen Display System'])]
    public function render()
    {
        $query = Order::with(['items.menuItem', 'items.variant', 'table'])
            ->where('restaurant_id', $this->restaurant->id);

        if (!$this->restaurant->kasir_direct_to_kds) {
            $query->where(function($q) {
                $q->whereIn('payment_status', ['paid', 'partial']); // Tunggu kasir konfirmasi pembayaran (paid/partial)
            });
        }

        $orders = $query->whereIn('status', ['pending', 'confirmed', 'cooking', 'ready_to_serve'])
            ->orderBy('created_at', 'asc') // Oldest first
            ->get()
            ->map(function($order) {
                // Map 'confirmed' status to 'pending' for the Kanban board grouping
                if ($order->status === 'confirmed') {
                    $order->display_status = 'pending';
                } else {
                    $order->display_status = $order->status;
                }
                return $order;
            })
            ->groupBy('display_status');

        return view('livewire.kitchen.board', [
            'orders' => $orders
        ]);
    }
}
