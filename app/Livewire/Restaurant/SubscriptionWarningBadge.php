<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;
use Filament\Facades\Filament;

class SubscriptionWarningBadge extends Component
{
    public $daysLeft = null;
    public $isValid = false;
    public $tenantSlug;

    public function mount()
    {
        $tenant = Filament::getTenant();
        
        if (!$tenant) return;

        $this->tenantSlug = $tenant->slug;
        $owner = $tenant->owner;

        if (!$owner) return;

        $sub = $owner->activeSubscription;
        if (!$sub) return;

        $settings = app(\App\Settings\GeneralSettings::class);
        $thresholdDays = $settings->subscription_expiry_warning_days ?? 7;
        
        $this->daysLeft = (int) now()->diffInDays($sub->expires_at, false);
        
        if ($this->daysLeft >= 0 && $this->daysLeft <= $thresholdDays) {
            $this->isValid = true;
        }
    }

    public function render()
    {
        return view('livewire.restaurant.subscription-warning-badge');
    }
}
