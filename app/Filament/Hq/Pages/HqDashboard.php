<?php

namespace App\Filament\Hq\Pages;

use Filament\Pages\Page;
use App\Models\Restaurant;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HqDashboard extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'HQ Overview';
    protected static ?string $title           = 'Franchise HQ Dashboard';
    protected static ?string $slug            = 'dashboard';
    protected static ?int    $navigationSort  = -1;

    protected static string $view = 'filament.hq.pages.hq-dashboard';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        // 1. Super Admin selalu bisa akses
        if ($user->hasRole('super_admin')) return true;

        // 2. Owner harus punya langganan AKTIF (paket apa saja)
        $sub = $user->activeSubscription;
        return $user->hasRole('restaurant_owner') && $sub && $sub->isValid();
    }

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        $firstRestaurant = $user->hasRole('super_admin')
            ? \App\Models\Restaurant::first()
            : \App\Models\Restaurant::withoutGlobalScope('tenant')->where('user_id', $user->id)->first();

        $url = $firstRestaurant ? "/restaurants/{$firstRestaurant->slug}" : '/restaurants';

        return [
            \Filament\Actions\Action::make('back_to_restaurant')
                ->label('Kembali ke Restoran')
                ->url($url)
                ->icon('heroicon-m-arrow-left')
                ->color('warning'),
        ];
    }

    public function getViewData(): array
    {
        $user = auth()->user();

        // Ambil semua restoran milik user (bypass tenant scope)
        $restaurants = $user->hasRole('super_admin')
            ? Restaurant::all()
            : Restaurant::withoutGlobalScope('tenant')->where('user_id', $user->id)->get();

        $restaurantIds = $restaurants->pluck('id');

        // Statistik Global (Hari Ini)
        $today = Carbon::today();

        $totalRevenueToday = Order::whereIn('restaurant_id', $restaurantIds)
            ->whereDate('created_at', $today)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $totalOrdersToday = Order::whereIn('restaurant_id', $restaurantIds)
            ->whereDate('created_at', $today)
            ->count();

        $activeWaitersToday = DB::table('waiter_calls')
            ->whereIn('restaurant_id', $restaurantIds)
            ->whereDate('created_at', $today)
            ->count();

        // Performa Per Cabang
        $branchPerformance = $restaurants->map(function ($rest) use ($today) {
            $salesToday  = $rest->orders()->whereDate('created_at', $today)->where('payment_status', 'paid')->sum('total_amount');
            $orderCount  = $rest->orders()->whereDate('created_at', $today)->count();
            $pendingOrders = $rest->orders()->whereIn('status', ['pending', 'confirmed', 'cooking'])->count();

            return [
                'name'        => $rest->name,
                'slug'        => $rest->slug,
                'sales_today' => $salesToday,
                'orders_today'=> $orderCount,
                'pending'     => $pendingOrders,
                'is_active'   => $rest->is_active,
            ];
        })->sortByDesc('sales_today');

        // Chart 7 Hari
        $chartData = $this->getLast7DaysStats($restaurantIds);

        return [
            'totalRevenueToday'  => $totalRevenueToday,
            'totalOrdersToday'   => $totalOrdersToday,
            'activeWaitersToday' => $activeWaitersToday,
            'branchPerformance'  => $branchPerformance,
            'chartData'          => $chartData,
            'branchCount'        => $restaurants->count(),
        ];
    }

    private function getLast7DaysStats($restaurantIds): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date    = Carbon::today()->subDays($i);
            $revenue = Order::whereIn('restaurant_id', $restaurantIds)
                ->whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->sum('total_amount');

            $data['labels'][] = $date->format('d M');
            $data['values'][] = (int) $revenue;
        }
        return $data;
    }
}
