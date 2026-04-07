<?php

namespace App\Filament\Restaurant\Pages;

use Filament\Pages\Page;
use App\Models\Order;
use App\Models\User;
use App\Filament\Restaurant\Resources\OrderResource;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class StaffPerformance extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static string $view = 'filament.restaurant.pages.staff-performance';

    protected static ?string $title = 'Analisis Performa Karyawan';

    protected static ?string $navigationLabel = 'Performa Karyawan';

    protected static ?string $navigationGroup = 'OPERASIONAL';

    protected static ?int $navigationSort = 5;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->hasFeature('Staff Performance Tracking') && 
               (auth()->user()->can('page_StaffPerformance') || auth()->user()->hasRole('restaurant_owner') || auth()->user()->hasRole('super_admin'));
    }

    public function mount(): void
    {
        if (! auth()->user()->hasFeature('Staff Performance Tracking')) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Fitur ini eksklusif untuk paket Empire.')
                ->danger()
                ->send();
            
            $this->redirect(OrderResource::getUrl('index'));
        }
    }

    protected function getViewData(): array
    {
        $tenantId = \Filament\Facades\Filament::getTenant()->id;

        // 1. Leaderboard Kasir (Processed Payments)
        $cashierStats = User::whereHas('ordersProcessed', function($q) use ($tenantId) {
                $q->where('restaurant_id', $tenantId);
            })
            ->withCount(['ordersProcessed' => function($q) use ($tenantId) {
                $q->where('restaurant_id', $tenantId)->whereIn('payment_status', ['paid', 'partial']);
            }])
            ->withSum(['ordersProcessed' => function($q) use ($tenantId) {
                $q->where('restaurant_id', $tenantId)->whereIn('payment_status', ['paid', 'partial']);
            }], 'total_amount')
            ->orderByDesc('orders_processed_count')
            ->get();

        // 2. Leaderboard Waiter/Kitchen (Served Orders)
        $waiterStats = User::whereHas('ordersServed', function($q) use ($tenantId) {
                $q->where('restaurant_id', $tenantId);
            })
            ->withCount(['ordersServed' => function($q) use ($tenantId) {
                $q->where('restaurant_id', $tenantId);
            }])
            // Gunakan addSelect untuk menambahkan kolom subquery tanpa menimpa default selection
            ->addSelect([
                'avg_serve_time' => Order::selectRaw('AVG(TIMESTAMPDIFF(MINUTE, cooking_started_at, served_at))')
                    ->whereColumn('served_by_id', 'users.id')
                    ->where('restaurant_id', $tenantId)
            ])
            ->orderByDesc('orders_served_count')
            ->get();

        return [
            'cashierStats' => $cashierStats,
            'waiterStats' => $waiterStats,
        ];
    }
}
