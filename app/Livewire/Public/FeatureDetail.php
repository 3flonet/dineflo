<?php

namespace App\Livewire\Public;

use App\Models\AppFeature;
use App\Settings\GeneralSettings;
use Livewire\Component;

class FeatureDetail extends Component
{
    public AppFeature $feature;

    public function mount(AppFeature $feature)
    {
        $this->feature = $feature;
    }

    public function render(GeneralSettings $settings)
    {
        $bullets = (array)($this->feature->bullets ?? []);
        $normalizedBullets = [];
        foreach ($bullets as $b) {
            if (is_array($b) && isset($b['bullet'])) {
                $normalizedBullets[] = $b['bullet'];
            } elseif (is_string($b)) {
                $normalizedBullets[] = $b;
            }
        }

        $plans = \App\Models\SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get();

        return view('livewire.public.feature-detail', [
            'settings' => $settings,
            'normalizedBullets' => $normalizedBullets,
            'plans' => $plans,
        ])->layout('components.layouts.app', ['hideLayoutFooter' => true]);
    }
}
