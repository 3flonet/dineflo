<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Carbon\Carbon;

class LicenseGracePeriodAlert extends Component
{
    public function render(): View|Closure|string
    {
        $isGracePeriod = session('license_is_grace_period', false);
        $gracePeriodUntil = session('license_grace_until');

        if (!$isGracePeriod || !$gracePeriodUntil) {
            return '';
        }

        try {
            $expiryDate = Carbon::parse($gracePeriodUntil);
            $now = Carbon::now();
            
            if ($now->isAfter($expiryDate)) {
                return '';
            }

            $daysRemaining = $now->diffInDays($expiryDate);
            $hoursRemaining = ceil($now->diffInHours($expiryDate) % 24);

            return view('components.license-grace-period-alert', [
                'gracePeriodUntil' => $gracePeriodUntil,
                'daysRemaining' => $daysRemaining,
                'hoursRemaining' => $hoursRemaining,
            ]);
        } catch (\Exception $e) {
            return '';
        }
    }
}
