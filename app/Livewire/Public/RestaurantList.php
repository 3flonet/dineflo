<?php

namespace App\Livewire\Public;

use Livewire\Component;

use App\Models\Restaurant;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('components.layouts.app', ['hideLayoutFooter' => true])]
class RestaurantList extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render(\App\Settings\GeneralSettings $settings)
    {
        $restaurants = Restaurant::where('is_active', true)
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('city', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(12);

        return view('livewire.public.restaurant-list', [
            'restaurants' => $restaurants,
            'settings' => $settings
        ])->layoutData(['title' => 'Direktori Restoran']);
    }
}
