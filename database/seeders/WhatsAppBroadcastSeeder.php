<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WhatsAppBroadcast;
use App\Models\Restaurant;
use Carbon\Carbon;

class WhatsAppBroadcastSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurant = Restaurant::where('slug', 'demo-resto')->first();

        if (!$restaurant) {
            $this->command->warn('Demo Restaurant (demo-resto) not found. Skipping WhatsAppBroadcastSeeder.');
            return;
        }

        // 1. Scheduled Broadcast (Future)
        WhatsAppBroadcast::firstOrCreate(
            ['restaurant_id' => $restaurant->id, 'name' => 'Pengumuman Menu Baru Pagi (WA)'],
            [
                'trigger_type' => 'manual',
                'content' => "Halo *{{member_name}}* ☀️\n\nPagi-pagi gini paling enak ngopi ditemenin croissant! 🥐\nMulai hari ini, ada menu sarapan baru di *{{restaurant_name}}*.\n\nDapatkan diskon 10% dengan kode:\n*[[voucher_code]]*\n\nYuk mampir sebelum kehabisan!",
                'status' => 'scheduled',
                'scheduled_at' => Carbon::tomorrow()->setHour(8)->setMinute(0),
                'total_recipients' => 0,
                'sent_count' => 0,
                'read_count' => 0,
                'is_active' => true,
                'segmentation_type' => 'all',
                'target_tiers' => null,
            ]
        );

        // 2. Draft Broadcast
        WhatsAppBroadcast::firstOrCreate(
            ['restaurant_id' => $restaurant->id, 'name' => 'Undangan Komunitas Gold (WA Draft)'],
            [
                'trigger_type' => 'manual',
                'content' => "Khusus untukmu *{{member_name}}*! 🏅\n\nSebagai member Gold *{{restaurant_name}}*, kami mengundangmu ke acara gathering eksklusif minggu depan.\n\nJangan lupa tunjukkan pesan ini agar dapat free entry & welcome drink! 🥂\nPoin loyalty-mu saat ini: *{{points_balance}}*.",
                'status' => 'draft',
                'scheduled_at' => null,
                'total_recipients' => 0,
                'sent_count' => 0,
                'read_count' => 0,
                'is_active' => false, // Not active yet
                'segmentation_type' => 'tiers',
                'target_tiers' => ['gold'], // Only gold members
            ]
        );

        // 3. Completed Broadcast (Past)
        WhatsAppBroadcast::firstOrCreate(
            ['restaurant_id' => $restaurant->id, 'name' => 'Flash Sale Akhir Bulan LALU (WA)'],
            [
                'trigger_type' => 'manual',
                'content' => "🔥 FLASH SALE {{restaurant_name}} 🔥\n\nHanya hari ini *{{member_name}}*!\nDiskon 50% untuk semua Main Course. Buruan pakai kode voucher ini:\n*[[voucher_code]]*\n\nBerlaku sampai jam 21:00 malam ini.",
                'status' => 'completed',
                'scheduled_at' => Carbon::now()->subDays(5),
                'last_run_at' => Carbon::now()->subDays(5)->addMinutes(5),
                'total_recipients' => 15,
                'sent_count' => 15,
                'read_count' => 10,
                'is_active' => true,
                'segmentation_type' => 'all',
                'target_tiers' => null,
            ]
        );
        
        $this->command->info('✅ WhatsApp Broadcasts (Manual) seeded successfully!');
    }
}
