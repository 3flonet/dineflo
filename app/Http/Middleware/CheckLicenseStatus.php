<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\LicenseService;
use Carbon\Carbon;

class CheckLicenseStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip license check for installer, public routes, health checks, AND the license status page itself
        if ($request->is('install/*', 'up', 'admin/license*', 'admin/license/*') || !auth()->check() || !file_exists(storage_path('installed.lock'))) {
            return $next($request);
        }

        $service = app(LicenseService::class);
        $status = $service->check();

        // 🛡️ Always initialize
        $license = config('app.license', []);
        $graceUntil = $license['grace_until'] ?? null;

        // 1. Check for Major Issues (Status that should block eventually)
        $blockingStatuses = ['revoked', 'suspended', 'tampered', 'invalid_signature_or_response', 'deactivated', 'inactive', 'invalid', 'error', 'expired'];

        if (in_array($status, $blockingStatuses)) {
            $firstDetected = \App\Models\Setting::get('license_issue_detected_at');
            
            // If it's a new issue, record the timestamp
            if (!$firstDetected) {
                // Store raw string since Setting@get will decode it
                $firstDetected = now()->toDateTimeString();
                \App\Models\Setting::updateOrCreate(
                    ['name' => 'license_issue_detected_at'], 
                    ['payload' => json_encode($firstDetected), 'group' => 'general']
                );
            }

            // Setting@get already decodes, so firstDetected is already a string
            $firstDetectedTime = \Carbon\Carbon::parse($firstDetected);
            $isGracePeriod = $status === 'expired' ? true : $firstDetectedTime->diffInHours(now()) < 24;

            // If it's EXPIRED, use the actual grace period from license if available
            if ($status === 'expired' && $graceUntil && \Carbon\Carbon::parse($graceUntil)->isFuture()) {
                $isGracePeriod = true;
            }

            // If past grace period -> HARD LOCK
            if (!$isGracePeriod && (!isset($graceUntil) || \Carbon\Carbon::parse($graceUntil)->isPast())) {
                if ($request->header('X-Livewire')) {
                    return response('', 204)->header('X-Livewire-Redirect', url('/admin/license'));
                }
                return redirect('/admin/license');
            }

            // Still in grace period -> Store in session
            session([
                'license_status' => $status,
                'license_is_grace_period' => true,
                'license_expires_at' => $license['expires_at'] ?? null,
                'license_grace_until' => $graceUntil ?? $firstDetectedTime->addDay()->toDateTimeString(),
            ]);
        } else {
            \App\Models\Setting::where('name', 'license_issue_detected_at')->delete();
        }

        // 3. Store in session for UI Components (Banner, Status dot)
        session([
            'license_status' => $status,
            'license_expires_at' => $license['expires_at'] ?? null,
            'license_grace_until' => $graceUntil,
            'license_is_grace_period' => $status === 'expired' && $graceUntil && Carbon::parse($graceUntil)->isFuture(),
        ]);

        return $next($request);
    }
}
