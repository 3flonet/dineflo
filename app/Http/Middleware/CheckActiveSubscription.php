<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $tenant = Filament::getTenant();

        // 1. Skip if not logged in or no tenant context
        if (!$user || !$tenant) {
            return $next($request);
        }

        // 2. Super Admin always bypassed
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }


        // 4. Get the owner of the current restaurant
        $owner = $tenant->owner;

        if (!$owner) {
            return $next($request);
        }

        // 5. Check if the owner has an active subscription
        if (!$owner->activeSubscription()->exists()) {
            
            // If the user is the owner, redirect to subscription page in Restaurant Panel
            if ($user->id === $owner->id) {
                if ($request->routeIs('filament.restaurant.pages.my-subscription')) {
                    return $next($request);
                }

                \Filament\Notifications\Notification::make()
                    ->title('Langganan Diperlukan')
                    ->body('Anda perlu memiliki paket langganan aktif untuk mengakses fitur ini. Silakan pilih paket di bawah.')
                    ->warning()
                    ->send();

                return redirect()->route('filament.restaurant.pages.my-subscription', [
                    'tenant' => $tenant->slug
                ]);
            }

            // If the user is staff, show access denied until owner pays
            abort(403, 'Restoran ini belum memiliki paket langganan aktif. Silakan hubungi pemilik restoran.');
        }

        return $next($request);
    }
}
