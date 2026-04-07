<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":[]},{"name":"restaurant_owner","guard_name":"web","permissions":["page_FinancialInsights","view_ingredient_history","adjust_ingredient_stock","view_any_expense","view_expense","create_expense","update_expense","delete_expense","view_any_expense_category","view_expense_category","create_expense_category","update_expense_category","delete_expense_category","manage_feedback_rewards","view_feedback_rewards","split_bill_by_item","view_any_restaurant","create_restaurant","update_restaurant","delete_restaurant","view_restaurant","view_any_user","create_user","update_user","delete_user","view_user","view_any_role","create_role","update_role","delete_role","view_role","view_any_menu::category","view_menu::category","create_menu::category","update_menu::category","delete_menu::category","delete_any_menu::category","restore_menu::category","restore_any_menu::category","force_delete_menu::category","force_delete_any_menu::category","replicate_menu::category","reorder_menu::category","view_any_menu::item","view_menu::item","create_menu::item","update_menu::item","delete_menu::item","delete_any_menu::item","restore_menu::item","restore_any_menu::item","force_delete_menu::item","force_delete_any_menu::item","replicate_menu::item","reorder_menu::item","view_any_table","view_table","create_table","update_table","delete_table","delete_any_table","restore_table","restore_any_table","force_delete_table","force_delete_any_table","replicate_table","reorder_table","view_any_waiter::call","view_waiter::call","create_waiter::call","update_waiter::call","delete_waiter::call","delete_any_waiter::call","restore_waiter::call","restore_any_waiter::call","force_delete_waiter::call","force_delete_any_waiter::call","replicate_waiter::call","reorder_waiter::call","view_any_order","view_order","create_order","update_order","delete_order","view_any_member","view_member","create_member","update_member","delete_member","delete_any_member","restore_member","restore_any_member","force_delete_member","force_delete_any_member","replicate_member","reorder_member","page_Pos","page_Reports","page_MySubscription","page_KitchenDisplay","view_any_menu_item","create_menu_item","update_menu_item","delete_menu_item","view_any_menu_category","create_menu_category","update_menu_category","view_cash::drawer::log","view_any_cash::drawer::log","create_cash::drawer::log","update_cash::drawer::log","restore_cash::drawer::log","restore_any_cash::drawer::log","replicate_cash::drawer::log","reorder_cash::drawer::log","delete_cash::drawer::log","delete_any_cash::drawer::log","force_delete_cash::drawer::log","force_delete_any_cash::drawer::log","view_discount","view_any_discount","create_discount","update_discount","restore_discount","restore_any_discount","replicate_discount","reorder_discount","delete_discount","delete_any_discount","force_delete_discount","force_delete_any_discount","restore_expense","restore_any_expense","replicate_expense","reorder_expense","delete_any_expense","force_delete_expense","force_delete_any_expense","view_expense::category","view_any_expense::category","create_expense::category","update_expense::category","restore_expense::category","restore_any_expense::category","replicate_expense::category","reorder_expense::category","delete_expense::category","delete_any_expense::category","force_delete_expense::category","force_delete_any_expense::category","view_ingredient","view_any_ingredient","create_ingredient","update_ingredient","restore_ingredient","restore_any_ingredient","replicate_ingredient","reorder_ingredient","delete_ingredient","delete_any_ingredient","force_delete_ingredient","force_delete_any_ingredient","restore_order","restore_any_order","replicate_order","reorder_order","delete_any_order","force_delete_order","force_delete_any_order","view_order::feedback","view_any_order::feedback","create_order::feedback","update_order::feedback","restore_order::feedback","restore_any_order::feedback","replicate_order::feedback","reorder_order::feedback","delete_order::feedback","delete_any_order::feedback","force_delete_order::feedback","force_delete_any_order::feedback","view_pos::register::session","view_any_pos::register::session","create_pos::register::session","update_pos::register::session","restore_pos::register::session","restore_any_pos::register::session","replicate_pos::register::session","reorder_pos::register::session","delete_pos::register::session","delete_any_pos::register::session","force_delete_pos::register::session","force_delete_any_pos::register::session","view_reservation","view_any_reservation","create_reservation","update_reservation","restore_reservation","restore_any_reservation","replicate_reservation","reorder_reservation","delete_reservation","delete_any_reservation","force_delete_reservation","force_delete_any_reservation","restore_role","restore_any_role","replicate_role","reorder_role","delete_any_role","force_delete_role","force_delete_any_role","restore_user","restore_any_user","replicate_user","reorder_user","delete_any_user","force_delete_user","force_delete_any_user","page_InventoryAnalytics","page_KitchenPerformance","page_QuickLaunch","page_StaffPerformance","page_WithdrawPage","widget_SubscriptionAlert","widget_ReportStatsWidget","widget_RestaurantLowStockWidget","widget_TopCustomersWidget","widget_TopSellingItemsWidget","widget_StatsOverview","widget_ProfitLossOverview","widget_ExpenseChart","widget_SalesChart","widget_TopProducts","widget_UpcomingReservationsWidget","widget_LatestOrders","widget_PeakHoursChartWidget","widget_ProductCategoryChartWidget","widget_ReportChartWidget","view_branch","view_any_branch","create_branch","update_branch","restore_branch","restore_any_branch","replicate_branch","reorder_branch","delete_branch","delete_any_branch","force_delete_branch","force_delete_any_branch","page_HqDashboard","view_any_email_campaign","view_email_campaign","create_email_campaign","update_email_campaign","delete_email_campaign","delete_any_email_campaign","manage_email_marketing","view_email::broadcast","view_any_email::broadcast","create_email::broadcast","update_email::broadcast","restore_email::broadcast","restore_any_email::broadcast","replicate_email::broadcast","reorder_email::broadcast","delete_email::broadcast","delete_any_email::broadcast","force_delete_email::broadcast","force_delete_any_email::broadcast","view_email::campaign","view_any_email::campaign","create_email::campaign","update_email::campaign","restore_email::campaign","restore_any_email::campaign","replicate_email::campaign","reorder_email::campaign","delete_email::campaign","delete_any_email::campaign","force_delete_email::campaign","force_delete_any_email::campaign"]},{"name":"restaurant_admin","guard_name":"web","permissions":[]},{"name":"staff","guard_name":"web","permissions":[]},{"name":"waiter","guard_name":"web","permissions":[]},{"name":"kitchen","guard_name":"web","permissions":[]},{"name":"delivery","guard_name":"web","permissions":[]},{"name":"restaurant_admin","guard_name":"web","permissions":["view_any_user","create_user","update_user","delete_user","view_user","view_any_role","view_any_table","create_table","update_table","view_any_order","update_order","delete_order","view_any_menu_item","create_menu_item","update_menu_item","delete_menu_item","view_any_menu_category","create_menu_category","update_menu_category"]},{"name":"waiter","guard_name":"web","permissions":["view_any_table","view_table","view_any_order","view_order","create_order","update_order","view_any_menu_item"]},{"name":"kitchen","guard_name":"web","permissions":["view_any_order","view_order","update_order","view_any_menu_item"]},{"name":"staff","guard_name":"web","permissions":["view_any_user","view_any_order","update_order"]},{"name":"delivery","guard_name":"web","permissions":["view_any_order","view_order","update_order"]}]';
        $directPermissions = '{"287":{"name":"view_whats::app::broadcast","guard_name":"web"},"288":{"name":"view_any_whats::app::broadcast","guard_name":"web"},"289":{"name":"create_whats::app::broadcast","guard_name":"web"},"290":{"name":"update_whats::app::broadcast","guard_name":"web"},"291":{"name":"restore_whats::app::broadcast","guard_name":"web"},"292":{"name":"restore_any_whats::app::broadcast","guard_name":"web"},"293":{"name":"replicate_whats::app::broadcast","guard_name":"web"},"294":{"name":"reorder_whats::app::broadcast","guard_name":"web"},"295":{"name":"delete_whats::app::broadcast","guard_name":"web"},"296":{"name":"delete_any_whats::app::broadcast","guard_name":"web"},"297":{"name":"force_delete_whats::app::broadcast","guard_name":"web"},"298":{"name":"force_delete_any_whats::app::broadcast","guard_name":"web"},"299":{"name":"view_whats::app::campaign","guard_name":"web"},"300":{"name":"view_any_whats::app::campaign","guard_name":"web"},"301":{"name":"create_whats::app::campaign","guard_name":"web"},"302":{"name":"update_whats::app::campaign","guard_name":"web"},"303":{"name":"restore_whats::app::campaign","guard_name":"web"},"304":{"name":"restore_any_whats::app::campaign","guard_name":"web"},"305":{"name":"replicate_whats::app::campaign","guard_name":"web"},"306":{"name":"reorder_whats::app::campaign","guard_name":"web"},"307":{"name":"delete_whats::app::campaign","guard_name":"web"},"308":{"name":"delete_any_whats::app::campaign","guard_name":"web"},"309":{"name":"force_delete_whats::app::campaign","guard_name":"web"},"310":{"name":"force_delete_any_whats::app::campaign","guard_name":"web"}}';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
