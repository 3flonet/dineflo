<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CashDrawerLog;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;

class CashDrawerDemoSeeder extends Seeder
{
    public function run(): void
    {
        $restaurant = Restaurant::first();
        if (!$restaurant) return;

        $user = User::where('restaurant_id', $restaurant->id)->first() ?? User::first();
        $orders = Order::where('restaurant_id', $restaurant->id)->limit(5)->get();

        // 1. Automatic Logs (from sales)
        foreach ($orders as $order) {
            CashDrawerLog::create([
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'order_id' => $order->id,
                'type' => 'automatic',
                'reason' => "Pembayaran Pesanan #{$order->order_number}",
                'created_at' => now()->subHours(rand(1, 24)),
            ]);
        }

        // 2. Manual Logs (audit trail)
        $reasons = [
            'Tukar uang pecahan kecil (Rp 2.000 & Rp 5.000)',
            'Salah input kembalian, laci perlu dibuka ulang',
            'Memberikan kembalian untuk pesanan manual',
            'Pengecekan kas di tengah shift',
        ];

        foreach ($reasons as $reason) {
            CashDrawerLog::create([
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'type' => 'manual',
                'reason' => $reason,
                'created_at' => now()->subHours(rand(1, 48)),
            ]);
        }

        $this->command->info('✅ Cash Drawer Audit Logs seeded!');
    }
}
