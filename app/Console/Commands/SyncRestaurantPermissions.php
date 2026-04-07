<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Restaurant;
use App\Models\Role;
use Spatie\Permission\Models\Permission;

class SyncRestaurantPermissions extends Command
{
    protected $signature = 'dineflo:sync-permissions
                            {--restaurant= : ID restoran spesifik (opsional, kosong = semua restoran)}
                            {--dry-run    : Tampilkan apa yang akan dilakukan tanpa menyimpan perubahan apapun}';

    protected $description = 'Sync permissions ke semua role restoran. Aman dijalankan berulang kali (idempotent).';

    // ─────────────────────────────────────────────────────────────────────────
    // MASTER PERMISSION LIST
    //
    // Ini adalah SATU-SATUNYA sumber kebenaran untuk permission per role.
    //
    // ✅ SOP saat menambahkan fitur baru:
    //    1. Tambahkan permission baru di sini
    //    2. Tambahkan juga di RestaurantObserver (menggunakan method ini)
    //    3. Jalankan: php artisan dineflo:sync-permissions
    //
    // Catatan: Semua restoran (lama & baru) akan mendapatkan permission ini.
    // ─────────────────────────────────────────────────────────────────────────
    public static function getRolePermissions(): array
    {
        return [
            'restaurant_admin' => [
                // ── User Management ───────────────────────────────────────
                'view_any_user', 'view_user', 'create_user', 'update_user', 'delete_user',
                // ── Menu Item ─────────────────────────────────────────────
                'view_any_menu_item', 'view_menu_item', 'create_menu_item', 'update_menu_item', 'delete_menu_item',
                // ── Menu Category ─────────────────────────────────────────
                'view_any_menu_category', 'view_menu_category', 'create_menu_category', 'update_menu_category', 'delete_menu_category',
                // ── Order ─────────────────────────────────────────────────
                'view_any_order', 'view_order', 'create_order', 'update_order', 'delete_order',
                // ── Table ─────────────────────────────────────────────────
                'view_any_table', 'view_table', 'create_table', 'update_table', 'delete_table',
                // ── Role ──────────────────────────────────────────────────
                'view_any_role', 'view_role', 'update_role',
                // ── Gift Card ─────────────────────────────────────────────
                'view_any_gift_card', 'view_gift_card', 'create_gift_card', 'update_gift_card', 'delete_gift_card',
                // ── Discount / Voucher ────────────────────────────────────
                'view_any_discount', 'view_discount', 'create_discount', 'update_discount', 'delete_discount',
                // ── Expense ───────────────────────────────────────────────
                'view_any_expense', 'view_expense', 'create_expense', 'update_expense', 'delete_expense',
                'view_any_expense_category', 'view_expense_category', 'create_expense_category', 'update_expense_category', 'delete_expense_category',
                // ── Member ────────────────────────────────────────────────
                'view_any_member', 'view_member', 'create_member', 'update_member', 'delete_member',
                // ── Reservation ───────────────────────────────────────────
                'view_any_reservation', 'view_reservation', 'create_reservation', 'update_reservation', 'delete_reservation',
                // ── Ingredient ────────────────────────────────────────────
                'view_any_ingredient', 'view_ingredient', 'create_ingredient', 'update_ingredient', 'delete_ingredient',
                // ── Waiter Call ───────────────────────────────────────────
                'view_any_waiter_call', 'view_waiter_call',
                // ── Order Feedback ────────────────────────────────────────
                'view_any_order_feedback', 'view_order_feedback',
                // ── POS & Voucher ─────────────────────────────────────────
                'page_Pos', 'apply_voucher',
                // ── Marketing ─────────────────────────────────────────────
                'view_any_email_campaign', 'view_email_campaign', 'create_email_campaign', 'update_email_campaign',
                'view_any_whats_app_campaign', 'view_whats_app_campaign', 'create_whats_app_campaign', 'update_whats_app_campaign',
                'view_any_email_broadcast', 'create_email_broadcast',
                'view_any_whats_app_broadcast', 'create_whats_app_broadcast',
                // ── Cash Drawer ───────────────────────────────────────────
                'view_any_cash_drawer_log', 'view_cash_drawer_log',
                'view_any_pos_register_session', 'view_pos_register_session',
            ],

            'waiter' => [
                'view_any_order', 'view_order', 'create_order', 'update_order',
                'view_any_table', 'view_table',
                'view_any_menu_item', 'view_menu_item',
                'view_any_waiter_call',
                'page_Pos',
            ],

            'kitchen' => [
                'view_any_order', 'view_order', 'update_order',
                'view_any_menu_item', 'view_menu_item',
            ],

            'staff' => [
                'view_any_order', 'view_order', 'update_order',
                'view_any_user',
                'page_Pos', 'apply_voucher',
            ],

            'delivery' => [
                'view_any_order', 'view_order', 'update_order',
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────

    public function handle(): int
    {
        $isDryRun     = $this->option('dry-run');
        $restaurantId = $this->option('restaurant');

        if ($isDryRun) {
            $this->warn('⚠️  DRY RUN MODE — Tidak ada perubahan yang disimpan ke database');
            $this->newLine();
        }

        // Query restoran
        $query = Restaurant::query();
        if ($restaurantId) {
            $query->where('id', $restaurantId);
            if ($query->doesntExist()) {
                $this->error("Restoran dengan ID {$restaurantId} tidak ditemukan.");
                return Command::FAILURE;
            }
        }

        $restaurants = $query->get();
        $this->info("Memproses {$restaurants->count()} restoran...");
        $this->newLine();

        $bar        = $this->output->createProgressBar($restaurants->count());
        $totalAdded = 0;
        $totalNew   = 0;

        foreach ($restaurants as $restaurant) {
            [$added, $newRoles] = $this->syncForRestaurant($restaurant, $isDryRun);
            $totalAdded += $added;
            $totalNew   += $newRoles;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $verb = $isDryRun ? 'akan ditambahkan' : 'ditambahkan';
        $this->info("✅ Selesai!");
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Restoran diproses', $restaurants->count()],
                ["Permission baru {$verb}", $totalAdded],
                ["Role baru dibuat", $totalNew],
            ]
        );

        if ($isDryRun) {
            $this->newLine();
            $this->warn('Jalankan tanpa --dry-run untuk menerapkan perubahan.');
        }

        return Command::SUCCESS;
    }

    /**
     * Sync permissions untuk satu restoran.
     * Returns: [$permissionsAdded, $rolesCreated]
     */
    protected function syncForRestaurant(Restaurant $restaurant, bool $isDryRun): array
    {
        $permissionsAdded = 0;
        $rolesCreated     = 0;
        $rolePermissions  = self::getRolePermissions();

        foreach ($rolePermissions as $roleName => $permissions) {
            // Cari role yang scoped ke restoran ini
            $role = Role::where('name', $roleName)
                ->where('restaurant_id', $restaurant->id)
                ->first();

            // Buat role baru jika belum ada (untuk restoran lama yang mungkin belum punya semua role)
            if (!$role) {
                if (!$isDryRun) {
                    $role = Role::create([
                        'name'          => $roleName,
                        'guard_name'    => 'web',
                        'restaurant_id' => $restaurant->id,
                    ]);
                }
                $this->line("  [Resto #{$restaurant->id}] Role '{$roleName}' dibuat baru");
                $rolesCreated++;
            }

            foreach ($permissions as $permName) {
                // Pastikan permission global ada di DB
                $permission = Permission::firstOrCreate([
                    'name'       => $permName,
                    'guard_name' => 'web',
                ]);

                // Cek apakah role sudah memiliki permission ini
                $alreadyHas = $role && $role->hasPermissionTo($permName);

                if (!$alreadyHas) {
                    if (!$isDryRun && $role) {
                        $role->givePermissionTo($permission);
                    }
                    $this->line("  [Resto #{$restaurant->id}] '{$roleName}' ← '{$permName}'" . ($isDryRun ? ' (akan ditambahkan)' : ''));
                    $permissionsAdded++;
                }
            }
        }

        return [$permissionsAdded, $rolesCreated];
    }
}
