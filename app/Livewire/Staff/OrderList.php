<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Order;
use App\Models\Restaurant;

class OrderList extends Component
{
    use WithPagination;

    public Restaurant $restaurant;
    public $statusFilter = 'active'; // active, completed, all
    public $viewMode = 'orders'; // orders, tables, queues
    public $search = '';

    public function getListeners()
    {
        return [
            "echo:restaurant-queues.{$this->restaurant->id},.queue.updated" => 'onQueueUpdated',
            "echo-private:orders.{$this->restaurant->id},.order.created" => 'onOrderCreated',
            "echo-private:restaurant.{$this->restaurant->id},.waiter.called" => 'onWaiterCalled',
        ];
    }

    public function onQueueUpdated($data)
    {
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Status antrean diperbarui.'
        ]);
        // Note: We don't $refresh explicitly here if we want to avoid double render, 
        // but for pagination/etc, it's safer to just return and let Livewire handle it, 
        // or call $refresh.
    }

    public function onOrderCreated($data)
    {
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Pesanan baru masuk!'
        ]);
    }

    public function onWaiterCalled($data)
    {
        $this->dispatch('notify', [
            'type' => 'warning',
            'message' => "Panggilan dari Meja {$data['table_name']}!"
        ]);
    }

    public function setViewMode($mode)
    {
        if ($mode === 'tables' && !$this->restaurant->owner->hasFeature('Table Management System')) {
            $this->dispatch('notify', ['type' => 'danger', 'message' => 'Upgrade your subscription to use Advanced Table Management.']);
            return;
        }

        if ($mode === 'queues' && !$this->restaurant->owner->hasFeature('Queue Management System')) {
            $this->dispatch('notify', ['type' => 'danger', 'message' => 'Upgrade your subscription to use Queue Management System.']);
            return;
        }

        $this->viewMode = $mode;
        $this->resetPage();
    }

    // --- Queue Management Methods ---
    
    public function callQueue($id)
    {
        $queue = \App\Models\Queue::where('restaurant_id', $this->restaurant->id)->find($id);
        if ($queue) {
            $queue->update([
                'status' => 'calling',
                'called_at' => now()
            ]);
            // Logic for Sound/Broadcasting will be handled via Reverb (to be implemented)
            $this->dispatch('notify', ['type' => 'info', 'message' => "Antrean {$queue->full_number} sedang dipanggil."]);
            // Dispatch event for Reverb display
            event(new \App\Events\QueueUpdated($queue));
        }
    }

    public function skipQueue($id)
    {
        $queue = \App\Models\Queue::where('restaurant_id', $this->restaurant->id)->find($id);
        if ($queue) {
            $queue->update(['status' => 'skipped']);
            $this->dispatch('notify', ['type' => 'warning', 'message' => "Antrean {$queue->full_number} dilewati."]);
            event(new \App\Events\QueueUpdated($queue));
        }
    }

    public function cancelQueue($id)
    {
        $queue = \App\Models\Queue::where('restaurant_id', $this->restaurant->id)->find($id);
        if ($queue) {
            $queue->update(['status' => 'cancelled']);
            $this->dispatch('notify', ['type' => 'danger', 'message' => "Antrean {$queue->full_number} dibatalkan."]);
            event(new \App\Events\QueueUpdated($queue));
        }
    }

    public function seatedQueue($id, $tableId)
    {
        $queue = \App\Models\Queue::where('restaurant_id', $this->restaurant->id)->find($id);
        $table = \App\Models\Table::where('restaurant_id', $this->restaurant->id)->find($tableId);

        if ($queue && $table) {
            // Update Table Status
            $table->update(['status' => \App\Models\Table::STATUS_OCCUPIED]);

            // Update Queue Status
            $queue->update([
                'status' => 'seated',
                'table_id' => $tableId,
                'seated_at' => now()
            ]);

            $this->dispatch('notify', ['type' => 'success', 'message' => "Antrean {$queue->full_number} ditempatkan di Meja {$table->name}."]);
            event(new \App\Events\QueueUpdated($queue));
            // Trigger UI update for tables if they are in same view context (Service Dashboard)
        }
    }

    public function updateTableStatus($tableId, $status)
    {
        $table = \App\Models\Table::where('restaurant_id', $this->restaurant->id)->find($tableId);
        if ($table) {
            $table->update(['status' => $status]);
            $this->dispatch('notify', ['type' => 'success', 'message' => "Table {$table->name} status updated to {$status}."]);
        }
    }

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        
        if (auth()->check()) {
            setPermissionsTeamId($restaurant->id);
            auth()->user()->unsetRelation('roles');
            auth()->user()->unsetRelation('permissions');
        }

        if (!auth()->check() || 
            (!auth()->user()->can('view_any_order') && !auth()->user()->hasRole('restaurant_owner') && !auth()->user()->hasRole('super_admin'))) {
            abort(403, 'Unauthorized access to Staff Panel.');
        }
    }

    public function updateStatus($orderId, $newStatus)
    {
        $order = Order::where('restaurant_id', $this->restaurant->id)->find($orderId);
        if ($order) {
            $order->update(['status' => $newStatus]);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Order status updated.']);
        }
    }

    public function markAsPaid($orderId)
    {
        $order = Order::where('restaurant_id', $this->restaurant->id)->find($orderId);
        if ($order) {
            $order->update(['payment_status' => 'paid']);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Payment recorded successfully.']);
        }
    }

    #[Layout('components.layouts.app', ['title' => 'Staff Order Management'])]
    public function render()
    {
        $orders = [];
        $tables = [];
        $queues = [];

        if ($this->viewMode === 'orders') {
            $orders = Order::with(['table'])
                ->where('restaurant_id', $this->restaurant->id)
                ->when($this->statusFilter === 'active', function ($query) {
                    $query->where(function ($q) {
                        $q->whereIn('status', ['pending', 'confirmed', 'cooking', 'ready_to_serve'])
                            ->orWhere(function ($sub) {
                                $sub->where('status', 'completed')
                                    ->where('payment_status', 'unpaid');
                            });
                    });
                })
                ->when($this->statusFilter === 'completed', function ($query) {
                    return $query->where('status', 'completed')
                        ->where('payment_status', 'paid');
                })
                ->when($this->search, function ($query) {
                    return $query->where(function ($sub) {
                        $sub->where('customer_name', 'like', '%' . $this->search . '%')
                            ->orWhere('order_number', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20, pageName: 'ordersPage');
        } elseif ($this->viewMode === 'tables') {
            $tables = \App\Models\Table::where('restaurant_id', $this->restaurant->id)
                ->when($this->search, function ($query) {
                    return $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('area', 'like', '%' . $this->search . '%');
                })
                ->orderBy('name')
                ->paginate(24, pageName: 'tablesPage');
        } else {
            // Queue View
            $queues = \App\Models\Queue::where('restaurant_id', $this->restaurant->id)
                ->whereIn('status', ['waiting', 'calling'])
                ->when($this->search, function ($query) {
                    return $query->where('customer_name', 'like', '%' . $this->search . '%')
                        ->orWhere('prefix', 'like', '%' . $this->search . '%');
                })
                ->orderBy('created_at', 'asc')
                ->paginate(20, pageName: 'queuesPage');
        }

        return view('livewire.staff.order-list', [
            'orders' => $orders,
            'tables' => $tables,
            'queues' => $queues,
            'availableTables' => \App\Models\Table::where('restaurant_id', $this->restaurant->id)
                ->where('status', \App\Models\Table::STATUS_AVAILABLE)
                ->get(),
            'tableStatuses' => \App\Models\Table::getStatuses()
        ]);
    }

    public function setFilter($filter)
    {
        $this->statusFilter = $filter;
        $this->resetPage();
    }
}
