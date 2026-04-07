<?php

namespace App\Livewire\Public;

use App\Models\OrderFeedback;
use App\Models\SubscriptionPlan;
use App\Settings\GeneralSettings;
use Livewire\Component;

class LandingPage extends Component
{
    public function render(GeneralSettings $settings)
    {
        $plans = SubscriptionPlan::withoutGlobalScopes()
            ->where('is_active', true)
            ->orderBy('price')
            ->get();

        $testimonials = OrderFeedback::with(['order', 'restaurant'])
            ->where('is_public', true)
            ->where('rating', '>=', 4)
            ->latest()
            ->take(6)
            ->get();

        return view('livewire.public.landing-page', [
            'plans' => $plans,
            'testimonials' => $testimonials,
            'settings' => $settings,
        ])->layout('components.layouts.app', ['hideLayoutFooter' => true]);
    }
}
