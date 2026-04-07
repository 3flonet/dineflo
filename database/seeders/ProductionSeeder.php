<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use Spatie\Permission\Models\Permission;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This is designed for Clean Installs ONLY.
     * It sets up Roles, Permissions, and general structural data.
     */
    public function run(): void
    {
        // 1. Reset Spatie Cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Call Essential System Seeders
        $this->call([
            SettingsSeeder::class,
            SubscriptionPlanSeeder::class,
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
        ]);

        // 3. Create Global Roles & Permissions
        $this->command->info('Creating Production Global Roles & Permissions...');
        $this->createGlobalRoles();

        // 4. Create Default Navigation Settings
        $this->createDefaultSettings();
    }

    private function createGlobalRoles()
    {
        // Global Roles (Super Admin & Base Owner)
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $ownerRole = Role::firstOrCreate(['name' => 'restaurant_owner', 'guard_name' => 'web']);

        // Base Tenant Roles (Global Templates)
        $tenantRoles = ['restaurant_admin', 'staff', 'waiter', 'kitchen', 'delivery'];
        foreach ($tenantRoles as $role) {
             Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // --- DEFINE ALL PERMISSIONS ---
        $basicPerms = [
            'view_any_restaurant', 'create_restaurant', 'update_restaurant', 'delete_restaurant',
            'view_restaurant',
            'manage_payment_settings', 'manage_whatsapp_settings', 'manage_email_marketing', 'manage_feedback_rewards',
        ];

        $menuCategoryPerms = [
            'view_any_menu::category', 'view_menu::category', 'create_menu::category', 
            'update_menu::category', 'delete_menu::category', 'delete_any_menu::category',
            'restore_menu::category', 'restore_any_menu::category', 
            'force_delete_menu::category', 'force_delete_any_menu::category',
            'replicate_menu::category', 'reorder_menu::category',
        ];

        $menuItemPerms = [
            'view_any_menu::item', 'view_menu::item', 'create_menu::item', 
            'update_menu::item', 'delete_menu::item', 'delete_any_menu::item',
            'restore_menu::item', 'restore_any_menu::item', 
            'force_delete_menu::item', 'force_delete_any_menu::item',
            'replicate_menu::item', 'reorder_menu::item',
        ];

        $tablePerms = [
            'view_any_table', 'view_table', 'create_table', 
            'update_table', 'delete_table', 'delete_any_table',
            'restore_table', 'restore_any_table', 
            'force_delete_table', 'force_delete_any_table',
            'replicate_table', 'reorder_table',
        ];

        $waiterCallPerms = [
            'view_any_waiter::call', 'view_waiter::call', 'create_waiter::call', 
            'update_waiter::call', 'delete_waiter::call', 'delete_any_waiter::call',
            'restore_waiter::call', 'restore_any_waiter::call', 
            'force_delete_waiter::call', 'force_delete_any_waiter::call',
            'replicate_waiter::call', 'reorder_waiter::call',
        ];

        $orderPerms = [
             'view_any_order', 'view_order', 'create_order', 'update_order', 'delete_order',
        ];

        $memberPerms = [
            'view_any_member', 'view_member', 'create_member', 
            'update_member', 'delete_member', 'delete_any_member',
            'restore_member', 'restore_any_member', 
            'force_delete_member', 'force_delete_any_member',
            'replicate_member', 'reorder_member',
        ];

        $pagePerms = [
            'page_Pos', 'page_Reports', 'page_MySubscription', 'page_KitchenDisplay'
        ];

        $userRolePerms = [
            'view_any_user', 'create_user', 'update_user', 'delete_user', 'view_user',
            'view_any_role', 'view_role'
        ];

        $allPerms = array_merge(
            $basicPerms, $menuCategoryPerms, $menuItemPerms, $tablePerms,
            $waiterCallPerms, $orderPerms, $memberPerms, $pagePerms, $userRolePerms
        );

        // Create Permissions in DB
        foreach ($allPerms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // Owner Perms
        $ownerPerms = array_merge(
            $menuCategoryPerms, $menuItemPerms, $tablePerms, $waiterCallPerms,
            $orderPerms, $memberPerms, $pagePerms, $userRolePerms,
            ['view_restaurant', 'update_restaurant', 'manage_payment_settings', 'manage_whatsapp_settings', 'manage_email_marketing', 'manage_feedback_rewards']
        );

        $ownerRole->givePermissionTo($ownerPerms);
    }

    private function createDefaultSettings()
    {
        $this->command->info('Creating Default Platform Settings...');
        
        $settings = [
            'site_name' => 'DineFlow',
            'site_description' => 'Platform POS dan Manajemen Restoran Modern.',
            'site_keywords' => ['restaurant management system', 'POS', 'QR ordering'],
            'site_author' => 'Dineflo',
            'site_address' => 'Jakarta, Indonesia',
        ];

        foreach ($settings as $key => $value) {
            try {
                \Spatie\LaravelSettings\Models\SettingsProperty::updateOrCreate(
                    ['group' => 'general', 'name' => $key],
                    ['payload' => json_encode($value)]
                );
            } catch (\Exception $e) { }
        }
    }
}
