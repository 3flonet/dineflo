<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\MenuItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SimulateEmpireAnalyticsSeeder extends Seeder
{
    public function run(): void
    {
        $restaurant = Restaurant::first();
        if (!$restaurant) {
            $this->command->error('No restaurant found. Please create one first.');
            return;
        }

        $menus = MenuItem::where('restaurant_id', $restaurant->id)->get();
        if ($menus->isEmpty()) {
            $this->command->error('No menu items found for this restaurant.');
            return;
        }

        $staffUsers = User::all(); // Assuming all existing users can be used for simulation

        $this->command->info('Simulating 30 orders for restaurant: ' . $restaurant->name);

        for ($i = 1; $i <= 30; $i++) {
            // Random time today between 8 AM and 10 PM
            $orderTime = Carbon::today()->setHour(rand(8, 21))->setMinute(rand(0, 59));
            
            // Simulation Lifecycle:
            // 1. Order created (Waktu Pesan)
            // 2. Cooking started (Mulai Masak) - usually 2-10 mins after order
            $cookingStart = (clone $orderTime)->addMinutes(rand(2, 10));
            
            // 3. Cooking finished (Selesai Masak) - usually 5-30 mins after start
            $prepDuration = rand(5, 35);
            $cookingFinish = (clone $cookingStart)->addMinutes($prepDuration);
            
            // 4. Served (Di Serve) - usually 1-5 mins after finished
            $serveTime = (clone $cookingFinish)->addMinutes(rand(1, 5));

            $order = Order::create([
                'restaurant_id' => $restaurant->id,
                'order_number' => 'SIM-' . strtoupper(Str::random(6)),
                'status' => 'completed',
                'payment_status' => 'paid',
                'order_type' => 'dine_in',
                'customer_name' => 'Simulated Guest',
                'payment_method' => 'cash',
                'subtotal' => 0,
                'total_amount' => 0,
                'created_at' => $orderTime,
                'cooking_started_at' => $cookingStart,
                'cooking_finished_at' => $cookingFinish,
                'served_at' => $serveTime,
                'processed_by_id' => $staffUsers->random()->id,
                'served_by_id' => $staffUsers->random()->id,
            ]);

            $subtotal = 0;
            $itemsCount = rand(1, 3);
            for ($j = 0; $j < $itemsCount; $j++) {
                $menu = $menus->random();
                $qty = rand(1, 2);
                
                $variantId = null;
                $price = $menu->price;

                // Handle Variants
                if ($menu->price <= 0 && $menu->variants->count() > 0) {
                    $variant = $menu->variants->random();
                    $variantId = $variant->id;
                    $price = $variant->price;
                }
                
                $total = $price * $qty;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menu->id,
                    'menu_item_variant_id' => $variantId,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'total_price' => $total,
                ]);
                $subtotal += $total;
            }

            $order->update([
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
            ]);

            // MANUALLY TRIGGER OBSERVER if needed, 
            // but Order::create followed by update('status' => 'completed') 
            // in the loop above already handles it if status was dirty.
            // Actually, we set status => 'completed' in create, so it might not trigger 
            // updated() logic unless we do it in two steps or manual call.
            // Let's force a status update to trigger the observer logic.
            $order->status = 'pending'; $order->save();
            $order->status = 'completed'; $order->save();
        }

        $this->command->info('✅ Simulation data generated successfully!');
    }
}
