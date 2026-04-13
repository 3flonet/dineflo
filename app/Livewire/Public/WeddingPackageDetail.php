<?php

namespace App\Livewire\Public;

use App\Models\Restaurant;
use App\Models\WeddingPackage;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class WeddingPackageDetail extends Component
{
    public Restaurant $restaurant;
    public WeddingPackage $package;

    public function mount(Restaurant $restaurant, WeddingPackage $package)
    {
        $this->restaurant = $restaurant;
        $this->package = $package;
            
        // Security check
        if (!$this->restaurant->owner?->hasFeature('Wedding & Event Packages')) {
            abort(403, 'Fitur ini tidak tersedia untuk restoran ini.');
        }

        if (!$this->package->is_active) {
            abort(404);
        }
    }

    public function render()
    {
        return view('livewire.public.wedding-package-detail')
            ->layoutData([
                'title' => $this->package->name . ' - ' . $this->restaurant->name,
                'restaurant' => $this->restaurant,
                'hideLayoutFooter' => true,
            ]);
    }
}
