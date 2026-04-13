<?php

namespace App\Livewire\Public;

use Livewire\Component;

use App\Models\Restaurant;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class RestaurantProfile extends Component
{
    public Restaurant $restaurant;

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
    }

    public $showQueueModal = false;
    public $queueGuestCount = 1;
    public $queueCustomerName = '';
    public $queueCustomerPhone = '';
    public $createdQueue = null;

    public function incrementGuestCount()
    {
        $this->queueGuestCount++;
    }

    public function decrementGuestCount()
    {
        if ($this->queueGuestCount > 1) {
            $this->queueGuestCount--;
        }
    }

    public function takeQueue()
    {
        if (!$this->restaurant->owner?->hasFeature('Queue Management System')) {
            $this->dispatch('notify', ['type' => 'danger', 'message' => 'Fitur antrean online tidak tersedia.']);
            return;
        }
        $this->showQueueModal = true;
    }

    public function submitQueue()
    {
        $this->validate([
            'queueGuestCount' => 'required|integer|min:1',
            'queueCustomerName' => 'required|string|min:2',
            'queueCustomerPhone' => 'required|min:10',
        ]);

        $prefix = \App\Models\Queue::getPrefixByGuestCount($this->queueGuestCount);
        
        $lastQueue = \App\Models\Queue::where('restaurant_id', $this->restaurant->id)
            ->where('prefix', $prefix)
            ->whereDate('created_at', today())
            ->orderBy('queue_number', 'desc')
            ->first();
            
        $nextNumber = $lastQueue ? $lastQueue->queue_number + 1 : 1;

        $queue = \App\Models\Queue::create([
            'restaurant_id' => $this->restaurant->id,
            'customer_name' => $this->queueCustomerName,
            'customer_phone' => $this->queueCustomerPhone,
            'guest_count' => $this->queueGuestCount,
            'prefix' => $prefix,
            'queue_number' => $nextNumber,
            'status' => 'waiting',
            'source' => 'online',
        ]);

        $this->createdQueue = $queue;
        
        // Dispatch status update for Displays (TV)
        event(new \App\Events\QueueUpdated($queue));
    }

    public function getFeedbacksProperty()
    {
        return $this->restaurant->orderFeedbacks()
            ->where('is_public', true)
            ->latest()
            ->take(10)
            ->get();
    }

    public function render(\App\Settings\GeneralSettings $settings)
    {
        $hasRemoveBranding = $this->restaurant->owner?->hasFeature('Remove Branding');
        
        $facilities = $this->restaurant->facilities()
            ->with('photos')
            ->orderBy('sort_order')
            ->get();
        
        $hasWeddingFeature = $this->restaurant->owner?->hasFeature('Wedding & Event Packages');
        $weddingPackages = $hasWeddingFeature 
            ? $this->restaurant->weddingPackages()->where('is_active', true)->orderBy('sort_order')->get()
            : collect([]);
        
        return view('livewire.public.restaurant-profile', [
            'feedbacks' => $this->feedbacks,
            'settings' => $settings,
            'facilities' => $facilities,
            'weddingPackages' => $weddingPackages,
            'hasWeddingFeature' => $hasWeddingFeature,
        ])->layoutData([
            'title' => $this->restaurant->name,
            'restaurant' => $hasRemoveBranding ? $this->restaurant : null,
            'hideLayoutFooter' => true,
        ]);
    }
}
