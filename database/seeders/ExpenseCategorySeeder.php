<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;
use App\Models\Restaurant;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kategori Expense yang umum untuk restoran
        $categories = [
            // Cost of Goods (Bahan Baku & Inventory)
            [
                'name' => 'Bahan Baku Makanan',
                'type' => 'cost_of_goods',
                'is_active' => true,
            ],
            [
                'name' => 'Bahan Baku Minuman',
                'type' => 'cost_of_goods',
                'is_active' => true,
            ],
            [
                'name' => 'Kemasan & Packaging',
                'type' => 'cost_of_goods',
                'is_active' => true,
            ],
            
            // Operational
            [
                'name' => 'Listrik',
                'type' => 'utility',
                'is_active' => true,
            ],
            [
                'name' => 'Air & Gas',
                'type' => 'utility',
                'is_active' => true,
            ],
            [
                'name' => 'Internet & Telepon',
                'type' => 'utility',
                'is_active' => true,
            ],
            
            // Rent & Property
            [
                'name' => 'Sewa Tempat',
                'type' => 'rent',
                'is_active' => true,
            ],
            [
                'name' => 'Perawatan & Perbaikan',
                'type' => 'operational',
                'is_active' => true,
            ],
            
            // Salary & HR
            [
                'name' => 'Gaji Karyawan',
                'type' => 'salary',
                'is_active' => true,
            ],
            [
                'name' => 'Bonus & Insentif',
                'type' => 'salary',
                'is_active' => true,
            ],
            [
                'name' => 'BPJS & Asuransi',
                'type' => 'salary',
                'is_active' => true,
            ],
            
            // Marketing
            [
                'name' => 'Iklan & Promosi',
                'type' => 'marketing',
                'is_active' => true,
            ],
            [
                'name' => 'Komisi Delivery',
                'type' => 'marketing',
                'is_active' => true,
            ],
            
            // Other
            [
                'name' => 'Perlengkapan Kebersihan',
                'type' => 'operational',
                'is_active' => true,
            ],
            [
                'name' => 'Alat Tulis & Administrasi',
                'type' => 'operational',
                'is_active' => true,
            ],
            [
                'name' => 'Pajak & Retribusi',
                'type' => 'other',
                'is_active' => true,
            ],
            [
                'name' => 'Lain-lain',
                'type' => 'other',
                'is_active' => true,
            ],
        ];

        // Get all restaurants
        $restaurants = Restaurant::all();

        foreach ($restaurants as $restaurant) {
            foreach ($categories as $category) {
                ExpenseCategory::firstOrCreate(
                    [
                        'restaurant_id' => $restaurant->id,
                        'name' => $category['name'],
                    ],
                    [
                        'type' => $category['type'],
                        'is_active' => $category['is_active'],
                    ]
                );
            }
        }

        $this->command->info('✅ Expense categories seeded successfully for all restaurants!');
    }
}
