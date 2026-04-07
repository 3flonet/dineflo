@php
    $licenseStatus = session('license_status');
    $isGracePeriod = session('license_is_grace_period');
    $graceUntil = session('license_grace_until');
    $expiresAt = session('license_expires_at');
    
    // Safety check for Carbon
    $isExpiringSoon = false;
    try {
        if ($expiresAt) {
            $carbonExp = \Carbon\Carbon::parse($expiresAt);
            $isExpiringSoon = $carbonExp->isFuture() && $carbonExp->diffInDays(now()) <= 7;
        }
    } catch (\Throwable $e) { $isExpiringSoon = false; }
@endphp

@if($licenseStatus && (in_array($licenseStatus, ['expired', 'deactivated', 'suspended', 'inactive', 'invalid', 'error']) || $isExpiringSoon))
    <div style="background: @if($licenseStatus == 'active' || $isExpiringSoon) #f59e0b @else #ef4444 @endif; color: white; padding: 12px 20px; font-weight: 600; text-align: center; font-size: 14px; position: sticky; top: 0; z-index: 50; display: flex; justify-content: center; align-items: center; gap: 15px; border-bottom: 2px solid rgba(0,0,0,0.1);">
        <span style="font-size: 20px;">⚠️</span>
        
        <div>
            @if($isExpiringSoon && $licenseStatus == 'active')
                @php $days = ceil(\Carbon\Carbon::parse($expiresAt)->diffInDays(now())); @endphp
                Lisensi Anda akan berakhir dalam <strong>{{ $days }}</strong> hari lagi. 
            @elseif($isGracePeriod && $graceUntil)
                @php $hours = round(\Carbon\Carbon::parse($graceUntil)->diffInHours(now())); @endphp
                Masalah Lisensi! Sistem akan terkunci dalam <strong>{{ $hours }} jam</strong> kedepan. Harap segera periksa lisensi Anda.
            @else
                Masalah Lisensi Terdeteksi (Status: {{ strtoupper($licenseStatus) }}). Sistem akan segera terkunci.
            @endif
        </div>

        <a href="/admin/license" style="background: white; color: black; padding: 4px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 700; text-transform: uppercase; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            Kelola Lisensi
        </a>
    </div>
@endif
