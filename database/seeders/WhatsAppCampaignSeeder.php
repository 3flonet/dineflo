<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WhatsAppCampaign;
use App\Models\Restaurant;
use App\Models\Member;
use App\Models\Order;

class WhatsAppCampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurant = Restaurant::where('slug', 'demo-resto')->first();

        if (!$restaurant) {
            $this->command->warn('Demo Restaurant (demo-resto) not found. Skipping WhatsAppCampaignSeeder.');
            return;
        }

        // 1. Welcome Campaign
        WhatsAppCampaign::firstOrCreate(
            ['restaurant_id' => $restaurant->id, 'trigger_type' => 'welcome'],
            [
                'name' => 'Selamat Datang Member Baru (WA)',
                'content' => "Halo *{{member_name}}*! 🎁\n\nTerima kasih telah mendaftar menjadi member setia kami di *{{restaurant_name}}*.\n\nSebagai perkenalan, ada kode voucher spesial untuk kunjunganmu berikutnya:\n*[[voucher_code]]*\n\nSampai jumpa dan selamat menikmati hidangan kami! 🍽️",
                'target_tiers' => ['bronze', 'silver', 'gold'],
                'delay_days' => 0,
                'is_active' => true,
                'status' => 'active',
                'segmentation_type' => 'all',
            ]
        );

        // 2. Birthday Campaign
        WhatsAppCampaign::firstOrCreate(
            ['restaurant_id' => $restaurant->id, 'trigger_type' => 'birthday'],
            [
                'name' => 'Kado Ulang Tahun (WA)',
                'content' => "Selamat Ulang Tahun, *{{member_name}}*! 🎂🎉\n\nHari ini adalah hari spesialmu, dan *{{restaurant_name}}* ingin merayakannya bersamamu!\n\nTunjukkan pesan ini ke kasir atau gunakan kode voucher:\n*[[voucher_code]]*\nuntuk mengklaim kado kejutan atau dessert gratis dari kami.\n\nSemoga panjang umur dan sehat selalu! 🥳",
                'target_tiers' => ['bronze', 'silver', 'gold'],
                'delay_days' => 0, // Dijalankan hari H
                'is_active' => true,
                'status' => 'active',
                'segmentation_type' => 'all',
            ]
        );

        // 3. Win-back Campaign
        WhatsAppCampaign::firstOrCreate(
            ['restaurant_id' => $restaurant->id, 'trigger_type' => 'win_back'],
            [
                'name' => 'Kangen Customer Win-Back (WA)',
                'content' => "Kami merindukanmu, *{{member_name}}*! 🥺\n\nSudah lama nih kamu gak mampir ke *{{restaurant_name}}*. Poin loyalty-mu saat ini: *{{points_balance}} Poin* lho!\n\nAgar kamu makin semangat buat datang lagi, kami kasih voucher khusus kangen:\n*[[voucher_code]]*\n\nDitunggu kehadirannya ya! 🍲",
                'target_tiers' => ['bronze', 'silver', 'gold'],
                'delay_days' => 30, // 30 hari tidak aktif
                'is_active' => true,
                'status' => 'active',
                'segmentation_type' => 'all',
            ]
        );

        $this->command->info('✅ WhatsApp Marketing campaigns seeded successfully!');
    }
}
