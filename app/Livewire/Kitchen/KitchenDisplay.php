<?php

namespace App\Livewire\Kitchen;

use App\Models\Order;
use App\Models\Restaurant;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')] 
class KitchenDisplay extends Component
{
    public Restaurant $restaurant;
    public $loading = false; // For button loading state

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        $this->ensureDummyData();
    }

    protected function ensureDummyData()
    {
        if ($this->restaurant->menuItems()->count() === 0) {
            try {
                // Create Categories
                $foodCat = $this->restaurant->menuCategories()->firstOrCreate(
                    ['name' => 'Makanan Utama'], 
                    ['slug' => 'makanan-utama', 'is_active' => true, 'sort_order' => 1]
                );
                $drinkCat = $this->restaurant->menuCategories()->firstOrCreate(
                    ['name' => 'Minuman'], 
                    ['slug' => 'minuman', 'is_active' => true, 'sort_order' => 2]
                );

                // Create Items
                $nasgor = $this->restaurant->menuItems()->create([
                    'menu_category_id' => $foodCat->id,
                    'name' => 'Nasi Goreng Spesial',
                    'slug' => 'nasi-goreng-spesial', 
                    'description' => 'Nasgor enak',
                    'price' => 25000,
                    'is_available' => true,
                ]);
                $esteh = $this->restaurant->menuItems()->create([
                    'menu_category_id' => $drinkCat->id,
                    'name' => 'Es Teh Manis',
                    'slug' => 'es-teh-manis',
                    'price' => 5000,
                    'is_available' => true,
                ]);
                
                // Create Variants
                $varBiasa = $nasgor->variants()->create(['name' => 'Biasa', 'price' => 0]);
                $varJumbo = $nasgor->variants()->create(['name' => 'Jumbo', 'price' => 5000]);

                // Create Orders
                $this->restaurant->load('tables');
                $table = $this->restaurant->tables->first();
                
                if ($table) {
                    $order = $this->restaurant->orders()->create([
                        'table_id' => $table->id,
                        'customer_name' => 'Demo User',
                        'total_amount' => 30000,
                        'status' => 'pending',
                    ]);
                    \App\Models\OrderItem::create([
                        'order_id' => $order->id,
                        'menu_item_id' => $nasgor->id,
                        'quantity' => 1,
                        'unit_price' => 25000,
                        'menu_item_variant_id' => $varBiasa->id,
                        'total_price' => 25000,
                    ]);
                    \App\Models\OrderItem::create([
                        'order_id' => $order->id,
                        'menu_item_id' => $esteh->id,
                        'quantity' => 1,
                        'unit_price' => 5000,
                        'total_price' => 5000,
                    ]);
                }
            } catch (\Exception $e) {
                // Log error silently or dump
                // dd($e->getMessage()); 
            }
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

    public function updateStatus($orderId, $status)
    {
        $order = Order::find($orderId);
        if ($order && $order->restaurant_id == $this->restaurant->id) {
            // Check if moving to ready_to_serve, all items must be ready
            if ($status === 'ready_to_serve') {
                $allReady = $order->items->every('is_ready', true);
                if (!$allReady) {
                    $this->dispatch('notify', [
                        'type' => 'error',
                        'message' => 'Semua menu harus dicentang sebelum dipindahkan ke Siap Saji.'
                    ]);
                    return;
                }
            }

            $order->status = $status;
            $order->save();
        }
    }

    public function render()
    {
        // Fetch active orders only (exclude completed/cancelled)
        $query = Order::where('restaurant_id', $this->restaurant->id)
            ->whereIn('status', ['pending', 'confirmed', 'cooking', 'ready_to_serve'])
            ->with(['items.menuItem', 'items.variant', 'table'])
            ->orderBy('created_at', 'asc'); // Oldest first (FIFO)

        // Logic for kasir_direct_to_kds
        if (!$this->restaurant->kasir_direct_to_kds) {
            $query->whereIn('payment_status', ['paid', 'partial']);
        }

        $orders = $query->get();

        return view('livewire.kitchen.kitchen-display', [
            'orders' => $orders,
            'incomingOrders' => $orders->whereIn('status', ['pending', 'confirmed']),
            'cookingOrders' => $orders->where('status', 'cooking'),
            'readyOrders' => $orders->where('status', 'ready_to_serve'),
        ]);
    }
}
