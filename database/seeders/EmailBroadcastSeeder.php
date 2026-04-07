<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailCampaign;
use App\Models\Restaurant;

class EmailBroadcastSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurant = Restaurant::where('slug', 'demo-resto')->first();

        if (!$restaurant) {
            $this->command->warn('Demo Restaurant (demo-resto) not found. Skipping EmailBroadcastSeeder.');
            return;
        }

        // 1. New Menu Announcement Broadcast
        EmailCampaign::firstOrCreate(
            [
                'restaurant_id' => $restaurant->id, 
                'trigger_type' => 'manual',
                'name' => 'Pengumuman Menu Baru Spesial'
            ],
            [
                'subject' => 'Hai {{member_name}}, Cobain Menu Baru "Sultan Steak" Kami! 🍱',
                'content' => '
                    <p>Halo <strong>{{member_name}}</strong>,</p>
                    <p>Kabar gembira! <strong>{{restaurant_name}}</strong> baru saja meluncurkan menu terbaru kami yang sudah dinanti-nanti: <strong>Sultan Steak with Truffle Sauce</strong>.</p>
                    <p>Dibuat dengan daging wagyu pilihan dan saus truffle yang aromatik, menu ini siap memanjakan lidah Anda.</p>
                    <p>Khusus untuk member setia dengan tier <strong>{{tier}}</strong>, kami berikan promo spesial untuk kunjungan minggu ini!</p>
                    <p>Tunjukkan email ini saat berkunjung ke outlet kami.</p>
                    <p>Sampai jumpa!</p>
                ',
                'segmentation_type' => 'all',
                'target_tiers' => ['bronze', 'silver', 'gold'],
                'status' => 'draft',
                'is_active' => true,
            ]
        );

        $this->command->info('✅ Email Broadcast seeder completed successfully!');
    }
}
