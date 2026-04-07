<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class RestaurantOpeningHoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultOpeningHours = [
            ['day' => 'monday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            ['day' => 'tuesday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            ['day' => 'wednesday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            ['day' => 'thursday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            ['day' => 'friday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            ['day' => 'saturday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            ['day' => 'sunday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
        ];

        $restaurants = Restaurant::all();
        $count = 0;

        foreach ($restaurants as $restaurant) {
            $restaurant->update([
                'opening_hours' => $defaultOpeningHours
            ]);
            $count++;
        }

        $this->command->info("✅ Berhasil memperbarui data jam operasional untuk {$count} restoran.");
    }
}
