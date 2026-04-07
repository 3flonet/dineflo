<?php

namespace App\Livewire\Public;

use App\Models\Order;
use Livewire\Component;

class OrderTracking extends Component
{
    public $hash;
    public $order;

    public function mount($hash)
    {
        $this->hash = $hash;
        $this->loadOrder();
    }

    public function loadOrder()
    {
        $this->order = Order::with(['restaurant', 'items.menuItem', 'items.variant', 'table'])
            ->where('tracking_hash', $this->hash)
            ->first();

        if (!$this->order) {
            abort(404);
        }
    }

    public function refresh()
    {
        $this->loadOrder();
    }

    public function render()
    {
        return view('livewire.public.order-tracking')
            ->layout('components.layouts.app'); // We might need a generic public layout
    }
}
