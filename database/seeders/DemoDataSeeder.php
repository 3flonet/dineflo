<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use Spatie\Permission\Models\Permission;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // 1. Create Super Admin (Demo Mode Default)
            $this->command->info('Creating Default Super Admin (Demo Mode)...');
            $admin = User::firstOrCreate(
                ['email' => 'admin@dineflo.com'],
                [
                    'name' => 'Super Admin',
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );
            $admin->assignRole('super_admin');

            // 2. Create Test Owner & Demo Restaurant
            $this->command->info('Creating Demo Restaurant and Owner...');
            $owner = User::firstOrCreate(
                ['email' => 'owner@dineflo.com'],
                [
                    'name' => 'Demo Owner',
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );

            $restaurant = \App\Models\Restaurant::firstOrCreate(
                ['slug' => 'demo-resto'],
                [
                    'name' => 'Demo Restaurant',
                    'user_id' => $owner->id,
                    'opening_hours' => [
                        ['day' => 'monday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
                        ['day' => 'tuesday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
                        ['day' => 'wednesday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
                        ['day' => 'thursday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
                        ['day' => 'friday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
                        ['day' => 'saturday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
                        ['day' => 'sunday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
                    ],
                    // Pre-seed EDC Config for testing
                    'edc_config' => [
                        ['bank_name' => 'BCA', 'mdr_percent' => 0.15],
                        ['bank_name' => 'Mandiri', 'mdr_percent' => 0.15],
                        ['bank_name' => 'BRI', 'mdr_percent' => 0.15],
                    ],
                ]
            );

            // Assign role for the specific tenant
            setPermissionsTeamId($restaurant->id);
            $owner->assignRole('restaurant_owner');

            // 3. Create Active Subscription for Demo (Empire Plan)
            $plan = \App\Models\SubscriptionPlan::where('slug', 'empire')->first();
            if ($plan) {
                \App\Models\Subscription::firstOrCreate(
                    ['user_id' => $owner->id, 'subscription_plan_id' => $plan->id],
                    [
                        'status' => 'active',
                        'starts_at' => now(),
                        'expires_at' => now()->addDays(30),
                    ]
                );
            }

            // 4. Default Settings (SEO/Branding)
            $this->createDefaultSettings();

        } catch (\Exception $e) {
            $this->command->error('Demo Seeder Failed. Check laravel.log for details.');
            \Illuminate\Support\Facades\Log::error('Demo Seeder Failed: ' . $e->getMessage());
        }
    }

    private function createDefaultSettings()
    {
        $this->command->info('Creating Default SEO & Branding Settings...');
        
        $settings = [
            'site_name' => 'DineFlow',
            'site_description' => 'Tinggalkan cara manual..! Dineflo, Platform All-in-one terintegrasi untuk QR ordering, POS, KDS, dan analytics real-time untuk operasional restoran modern.',
            'site_keywords' => [
                'restaurant management system', 
                'restaurant POS', 
                'QR ordering system', 
                'kitchen display system', 
                'restaurant SaaS', 
                'restaurant operating system', 
                'multi tenant restaurant software', 
                'restaurant analytics', 
                'cloud POS restaurant'
            ],
            'site_author' => '3FLO',
            'site_twitter_handle' => '@dineflow',
            'site_og_image' => null,
            'site_favicon' => null,
            'site_logo' => null,
            'site_address' => 'Jakarta, Indonesia',
            'site_phone' => null,
        ];

        foreach ($settings as $key => $value) {
            try {
                \Spatie\LaravelSettings\Models\SettingsProperty::updateOrCreate(
                    ['group' => 'general', 'name' => $key],
                    ['payload' => json_encode($value)]
                );
            } catch (\Exception $e) {
                // Ignore if table not ready or other issue
            }
        }
    }
}
