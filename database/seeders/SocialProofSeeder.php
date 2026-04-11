<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialProofSeeder extends Seeder
{
    public function run(): void
    {
        $logos = [
            ['name' => 'The Burger King', 'image' => null],
            ['name' => 'Sushi Tei', 'image' => null],
            ['name' => 'Bakmi GM', 'image' => null],
            ['name' => 'Union Deli', 'image' => null],
            ['name' => 'Holycow Steak', 'image' => null],
            ['name' => 'Kopi Kenangan', 'image' => null],
        ];

        $testimonials = [
            [
                'name' => 'Budi Santoso',
                'role' => 'Owner of Bakso Solo Legend',
                'quote' => 'Dineflo sangat membantu proses antrean di warung saya. Gak ada lagi kertas order hilang atau salah baca tulisan tangan!',
                'avatar' => null,
                'rating' => 5
            ],
            [
                'name' => 'Jessica Wijaya',
                'role' => 'Manager of Trendy Coffee Bar',
                'quote' => 'Sistem QR Order & Pay-nya keren banget. Pelanggan merasa praktis karena bisa pesan langsung dari meja tanpa harus manggil-manggil pelayan.',
                'avatar' => null,
                'rating' => 5
            ]
        ];

        DB::table('settings')->upsert(
            [
                'group' => 'general',
                'name' => 'landing_partner_logos',
                'payload' => json_encode($logos),
                'locked' => false
            ],
            ['group', 'name'],
            ['payload']
        );

        DB::table('settings')->upsert(
            [
                'group' => 'general',
                'name' => 'landing_testimonials',
                'payload' => json_encode($testimonials),
                'locked' => false
            ],
            ['group', 'name'],
            ['payload']
        );

        $this->command->info('✅ Social Proof settings seeded successfully!');
    }
}
