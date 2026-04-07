<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;
use Carbon\Carbon;

class LicenseInfoWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.license-info-widget';

    protected static ?int $sort = 1;

    public function getLicenseInfo(): array
    {
        $license = config('app.license');
        
        $status = $license['status'] ?? 'inactive';
        $graceUntil = $license['grace_until'];
        $expiresAt = $license['expires_at'];

        $isGracePeriod = $status === 'expired' && $graceUntil && Carbon::parse($graceUntil)->isFuture();
        
        $statusColor = match ($status) {
            'active' => 'success',
            'expired' => $isGracePeriod ? 'warning' : 'danger',
            'revoked', 'suspended' => 'danger',
            'inactive' => 'gray',
            default => 'gray',
        };

        $statusLabel = match ($status) {
            'active' => 'Aktif',
            'expired' => $isGracePeriod ? 'Grace Period' : 'Kadaluarsa',
            'revoked' => 'Dicabut',
            'suspended' => 'Ditangguhkan',
            'inactive' => 'Tidak Aktif',
            default => ucfirst($status),
        };

        $lastPingAt = $license['last_ping_at'];
        $lastPingTime = $lastPingAt ? Carbon::parse($lastPingAt)->diffForHumans() : 'Belum pernah';

        return [
            'license_key' => $license['key'] ?: 'Belum Dikonfigurasi',
            'status' => $status,
            'status_label' => $statusLabel,
            'status_color' => $statusColor,
            'domain' => $license['domain'] ?: '-',
            'customer_name' => $license['customer']['name'] ?: '-',
            'last_ping' => $lastPingTime,
            'grace_period_until' => $graceUntil ? Carbon::parse($graceUntil)->format('d M Y H:i') : '-',
            'expires_at' => $expiresAt ? Carbon::parse($expiresAt)->format('d M Y H:i') : '-',
            'is_grace_period' => $isGracePeriod,
            'is_configured' => !!$license['key'],
        ];
    }

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()?->hasRole('super_admin');
    }
}
