<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailCampaign;
use App\Models\Restaurant;
use App\Models\Discount;

class EmailCampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurant = Restaurant::where('slug', 'demo-resto')->first();

        if (!$restaurant) {
            $this->command->warn('Demo Restaurant (demo-resto) not found. Skipping EmailCampaignSeeder.');
            return;
        }

        // 1. Welcome Campaign
        EmailCampaign::firstOrCreate(
            ['restaurant_id' => $restaurant->id, 'trigger_type' => 'welcome'],
            [
                'name' => 'Selamat Datang Member Baru',
                'subject' => 'Halo {{member_name}}, Selamat Bergabung di {{restaurant_name}}! 🎁',
                'content' => '
                    <p>Halo <strong>{{member_name}}</strong>,</p>
                    <p>Terima kasih telah bergabung menjadi member setia kami di {{restaurant_name}}. Sebagai tanda perkenalan, kami memberikan hadiah spesial untuk Anda!</p>
                    <p>Gunakan kode voucher berikut untuk mendapatkan diskon pada kunjungan Anda berikutnya:</p>
                    <h2 style="text-align: center;">[[voucher_code]]</h2>
                    <p>Sampai jumpa di {{restaurant_name}}!</p>
                ',
                'target_tiers' => ['bronze', 'silver', 'gold'],
                'delay_days' => 0,
                'is_active' => true,
            ]
        );

        // 2. Birthday Campaign
        EmailCampaign::firstOrCreate(
            ['restaurant_id' => $restaurant->id, 'trigger_type' => 'birthday'],
            [
                'name' => 'Kado Ulang Tahun Customer',
                'subject' => 'Selamat Ulang Tahun {{member_name}}! 🎂 Ada Kado Spesial Untukmu',
                'content' => '
                    <p>Halo <strong>{{member_name}}</strong>,</p>
                    <p>Selamat ulang tahun! 🎉 Di hari spesial Anda ini, {{restaurant_name}} ingin ikut merayakan dengan memberikan traktiran spesial.</p>
                    <p>Tunjukkan email ini atau gunakan kode voucher di bawah saat bertransaksi untuk mendapatkan <strong>Gratis Dessert</strong> atau diskon spesial:</p>
                    <h2 style="text-align: center;">[[voucher_code]]</h2>
                    <p>Selamat merayakan hari spesialmu!</p>
                ',
                'target_tiers' => ['bronze', 'silver', 'gold'],
                'delay_days' => 0, // Dijalankan 3 hari sebelum (logika di Job)
                'is_active' => true,
            ]
        );

        // 3. Win-back Campaign
        EmailCampaign::firstOrCreate(
            ['restaurant_id' => $restaurant->id, 'trigger_type' => 'win_back'],
            [
                'name' => 'Kangen Customer (Win-Back)',
                'subject' => 'Kami Merindukan Anda, {{member_name}}! 🥺',
                'content' => '
                    <p>Halo <strong>{{member_name}}</strong>,</p>
                    <p>Sudah lama kami tidak melihat Anda di {{restaurant_name}}. Kami sangat merindukan kehadiran Anda!</p>
                    <p>Agar makin semangat mampir lagi, ini ada voucher kangen khusus buat Anda:</p>
                    <h2 style="text-align: center;">[[voucher_code]]</h2>
                    <p>Ditunggu kedatangannya ya!</p>
                ',
                'target_tiers' => ['bronze', 'silver', 'gold'],
                'delay_days' => 30, // Default logika di Job (30 hari tidak aktif)
                'is_active' => true,
            ]
        );

        // 4. Create Sample Members for Triggers
        \App\Models\Member::firstOrCreate(
            ['restaurant_id' => $restaurant->id, 'email' => 'birthday-demo@example.com'],
            [
                'name' => 'Bambang Birthday',
                'whatsapp' => '6281234567891',
                'birthday' => now()->addDays(3), // Triggers in 3 days
                'points_balance' => 500,
            ]
        );

        \App\Models\Member::firstOrCreate(
            ['restaurant_id' => $restaurant->id, 'email' => 'winback-demo@example.com'],
            [
                'name' => 'Susi Soeridat',
                'whatsapp' => '6281234567892',
                'birthday' => now()->subYears(25),
                'points_balance' => 1200,
            ]
        );

        $winbackMember = \App\Models\Member::where('email', 'winback-demo@example.com')->first();
        if ($winbackMember && $winbackMember->orders()->count() === 0) {
            // Create a very old order to test win-back (inactive for > 30 days)
            $order = \App\Models\Order::create([
                'restaurant_id' => $restaurant->id,
                'member_id' => $winbackMember->id,
                'customer_name' => $winbackMember->name,
                'customer_phone' => $winbackMember->whatsapp,
                'order_number' => 'DNC-OLD-001',
                'total_amount' => 50000,
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => 'cash',
                'order_type' => 'dine_in',
                'created_at' => now()->subDays(45),
            ]);
        }

        $this->command->info('✅ Email Marketing campaigns & Demo Members seeded successfully!');
    }
}
