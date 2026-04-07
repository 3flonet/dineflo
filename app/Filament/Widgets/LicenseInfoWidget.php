<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class LicenseInfoWidget extends Widget
{
    protected static string $view = 'filament.widgets.license-info-widget';

    protected static ?int $sort = 1;

    public function getLicenseInfo(): array
    {
        $licenseKey = env('LICENSE_KEY');
        $licenseStatus = env('LICENSE_STATUS', 'inactive');
        $licenseDomain = env('LICENSE_DOMAIN');
        $customerName = env('LICENSE_CUSTOMER_NAME');
        $lastPingAt = env('LICENSE_LAST_PING_AT');
        $gracePeriodUntil = env('LICENSE_GRACE_UNTIL');
        $expiresAt = env('LICENSE_EXPIRES_AT');

        $isGracePeriod = $gracePeriodUntil && Carbon::parse($gracePeriodUntil)->isFuture();
        $statusColor = match ($licenseStatus) {
            'active' => 'success',
            'expired' => 'danger',
            'grace_period' => 'warning',
            'inactive' => 'gray',
            default => 'gray',
        };

        $statusLabel = match ($licenseStatus) {
            'active' => 'Aktif',
            'expired' => 'Kadaluarsa',
            'grace_period' => 'Grace Period',
            'inactive' => 'Tidak Aktif',
            default => 'Unknown',
        };

        $lastPingTime = $lastPingAt ? Carbon::parse($lastPingAt)->diffForHumans() : 'Belum pernah';

        return [
            'license_key' => $licenseKey ?: 'Not Configured',
            'status' => $licenseStatus,
            'status_label' => $statusLabel,
            'status_color' => $statusColor,
            'domain' => $licenseDomain ?: '-',
            'customer_name' => $customerName ?: '-',
            'last_ping' => $lastPingTime,
            'grace_period_until' => $gracePeriodUntil ?: '-',
            'expires_at' => $expiresAt ?: '-',
            'is_grace_period' => $isGracePeriod,
            'is_configured' => !!$licenseKey,
        ];
    }

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }
}
