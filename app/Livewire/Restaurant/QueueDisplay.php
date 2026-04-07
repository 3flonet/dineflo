<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;
use App\Models\Restaurant;
use App\Models\Queue;

class QueueDisplay extends Component
{
    public Restaurant $restaurant;
    public $callingQueues = [];
    public $waitingQueues = [];
    public $historyQueues = [];
    public $promotions = [];
    public $runningText = '';

    protected function getListeners()
    {
        return [
            "echo:restaurant-queues.{$this->restaurant->id},.queue.updated" => 'refreshQueues',
        ];
    }

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        
        if (!$this->restaurant->owner?->hasFeature('Queue Management System')) {
            abort(403, 'Queue Management System is not active for this restaurant.');
        }

        $this->refreshQueues();
    }

    public function refreshQueues($data = null)
    {
        // Get up to 3 currently being called (last changed to 'calling')
        $this->callingQueues = Queue::with('table')->where('restaurant_id', $this->restaurant->id)
            ->where('status', 'calling')
            ->orderBy('called_at', 'desc')
            ->take(3)
            ->get();

        // Get waiting list
        $this->waitingQueues = Queue::with('table')->where('restaurant_id', $this->restaurant->id)
            ->where('status', 'waiting')
            ->orderBy('created_at', 'asc')
            ->take(12)
            ->get();

        // Get recently seated/skipped (History)
        $this->historyQueues = Queue::with('table')->where('restaurant_id', $this->restaurant->id)
            ->whereIn('status', ['seated', 'skipped'])
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();

        // Fetch Promotions
        $this->promotions = \App\Models\QueuePromotion::where('restaurant_id', $this->restaurant->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // Fetch Running Text
        $this->runningText = $this->restaurant->queue_display_running_text ?? '';
            
        if ($data && isset($data['queue'])) {
            $updatedQueue = $data['queue'];
            if ($updatedQueue['status'] === 'calling') {
                $this->dispatch('triggerCall', [
                    'number' => $updatedQueue['full_number'],
                    'customer' => $updatedQueue['customer_name'] ?? 'Pelanggan'
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.restaurant.queue-display')
            ->layout('components.layouts.blank');
    }
}
