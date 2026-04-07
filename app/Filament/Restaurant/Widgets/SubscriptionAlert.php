<?php

namespace App\Filament\Restaurant\Widgets;

use Filament\Widgets\Widget;
use App\Models\User;
use Illuminate\Support\Carbon;

class SubscriptionAlert extends Widget
{
    protected static ?int $sort = -2; // Always on top

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full'; // Full width alert

    protected static string $view = 'filament.restaurant.widgets.subscription-alert';

    /**
     * Widget ini hanya relevan untuk owner restoran.
     * Staff tidak butuh melihat alert langganan karena tidak bisa mengelolanya.
     */
    public static function canView(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'restaurant_owner']);
    }

    public bool $visible = false;
    public string $color = 'primary';
    public string $icon = 'heroicon-o-information-circle';
    public string $title = '';
    public string $message = '';
    public string $url = '';

    public function mount()
    {
        $user = auth()->user();
        if ($user->hasRole('super_admin')) {
             $this->visible = false;
             return;
        }

        $this->url = route('filament.restaurant.pages.my-subscription', ['tenant' => \Filament\Facades\Filament::getTenant()->slug]);

        // Resolve ke owner restoran jika user adalah staff.
        // Staff tidak punya subscription sendiri — ikuti pola User::hasFeature() & getLimits().
        $owner = $user;
        $tenant = \Filament\Facades\Filament::getTenant();

        if ($tenant && $user->restaurant_id === $tenant->id && $tenant->user_id !== $user->id) {
            $owner = $tenant->owner;
        }

        // Jika user adalah staff, sembunyikan alert (staff tidak bisa upgrade/renew)
        if ($owner && $owner->id !== $user->id) {
            $this->visible = false;
            return;
        }

        $sub = $owner ? $owner->activeSubscription : null;
        
        // 1. No Active Subscription (Expired or New)
        if (! $sub) {
            $lastSub = $owner->currentSubscription;
            
            // Check if user has ANY subscription history
            if ($lastSub && $lastSub->isValid() === false) {
                // Expired
                $this->visible = true;
                $this->color = 'danger';
                $this->icon = 'heroicon-o-exclamation-triangle';
                $this->title = 'Langganan Berakhir';
                $this->message = 'Masa langganan Anda telah habis. Silakan lakukan perpanjangan untuk mendapatkan kembali akses penuh.';
            } else {
                // New User (Free/Trial Mode)
                // Maybe don't show annoying alert if they just registered.
                // Or show friendly 'Upgrade to Pro' banner.
                $this->visible = true;
                $this->color = 'info';
                $this->icon = 'heroicon-o-sparkles';
                $this->title = 'Tingkatkan Paket Anda';
                $this->message = 'Buka lebih banyak fitur seperti menu tanpa batas dan dukungan multi-restoran dengan upgrade ke paket Pro.';
            }
            return;
        }

        // 2. Has Active Subscription - Check Expiry
        if ($sub->expires_at) {
            $daysLeft = (int) now()->diffInDays($sub->expires_at, false);
            
            if ($daysLeft < 0) {
                 // Technically expired but status still active in DB
                 $this->visible = true;
                 $this->color = 'danger';
                 $this->title = 'Langganan Berakhir';
                 $this->message = 'Masa langganan Anda telah berakhir hari ini.';
            } elseif ($daysLeft <= app(\App\Settings\GeneralSettings::class)->subscription_expiry_warning_days) {
                // Nearing Expiry
                $this->visible = true;
                $this->color = 'warning';
                $this->icon = 'heroicon-o-clock';
                $this->title = 'Masa Langganan Hampir Habis';
                $this->message = "Langganan Anda akan berakhir dalam {$daysLeft} hari (" . $sub->expires_at->translatedFormat('d M Y') . "). Segera lakukan perpanjangan.";
            }
        }
    }
}
