<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\SocialPost;
use App\Settings\GeneralSettings;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Community Wall - Dineflo')]
class SocialWall extends Component
{
    public function render()
    {
        $settings = app(GeneralSettings::class);
        $posts = SocialPost::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.public.social-wall', [
            'posts' => $posts,
            'settings' => $settings
        ])->title("Community Wall - " . $settings->site_name);
    }
}
