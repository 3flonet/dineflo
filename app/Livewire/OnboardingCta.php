<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Restaurant;
use Illuminate\Support\Str;

class OnboardingCta extends Component
{
    public $name = '';
    public $slug = '';
    public $isAvailable = null;

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
        $this->checkAvailability();
    }

    public function updatedSlug($value)
    {
        $this->slug = Str::slug($value);
        $this->checkAvailability();
    }

    public function checkAvailability()
    {
        if (empty($this->slug)) {
            $this->isAvailable = null;
            return;
        }

        $exists = Restaurant::where('slug', $this->slug)->exists();
        $this->isAvailable = !$exists;
    }

    public function startRegistration()
    {
        $this->validate([
            'name' => 'required|min:3',
            'slug' => 'required|min:3',
        ]);

        if (!$this->isAvailable) {
            return;
        }

        // Simpan ke session untuk diambil nanti setelah login/register
        session()->put('onboarding_restaurant_name', $this->name);
        session()->put('onboarding_restaurant_slug', $this->slug);
        session()->save();

        // Jika sudah login, langsung ke setup
        if (auth()->check()) {
            return redirect()->to('/restaurants/setup');
        }

        return redirect()->to(route('filament.restaurant.auth.register', [
            'name' => $this->name,
            'slug' => $this->slug
        ]));
    }

    public function render()
    {
        return view('livewire/onboarding-cta');
    }
}
