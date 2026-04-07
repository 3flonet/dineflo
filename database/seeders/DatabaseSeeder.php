<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Reset Spatie Cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->call([
            SettingsSeeder::class,
            SubscriptionPlanSeeder::class,
            EssentialRolesSeeder::class, // Modern split
            FinancialInsightsPermissionSeeder::class,
            IngredientStockPermissionSeeder::class,
            ExpenseCategorySeeder::class,
            ExpensePermissionSeeder::class,
            FeedbackRewardPermissionSeeder::class,
            SplitByItemPermissionSeeder::class,
            EmailMarketingPermissionSeeder::class,
            WhatsAppPermissionSeeder::class,
            OrderFeedbackPermissionSeeder::class,
            GiftCardPermissionSeeder::class,
            AppFeatureSeeder::class,
            PaymentSettingsPermissionSeeder::class,
        ]);

        if (config('app.seed_demo', false)) {
            $this->call([
                DemoDataSeeder::class,
                EmailCampaignSeeder::class,
                EmailBroadcastSeeder::class,
                WhatsAppCampaignSeeder::class,
                WhatsAppBroadcastSeeder::class,
            ]);
        }
        
        // Note: DemoDataSeeder handles roles, permissions, users, restaurant, menu, and orders.
    }
}
