<?php

namespace App\Filament\Concerns;

use Filament\Forms;
use Spatie\Permission\Models\Permission;

/**
 * Trait yang menyediakan logika grouping permission per resource
 * untuk digunakan di Admin Panel dan Restaurant Panel RoleResource.
 *
 * Setiap resource (Gift Card, Menu Item, Order, dll.) mendapat
 * Fieldset sendiri berisi CheckboxList aksi horizontal.
 */
trait HasResourceGroupedPermissions
{
    /** Aksi utama yang selalu ditampilkan */
    public static array $primaryActions = ['view_any', 'view', 'create', 'update', 'delete'];

    /** Aksi lanjutan — tampil dalam section kolapsibel */
    public static array $advancedActions = [
        'delete_any', 'restore', 'restore_any',
        'force_delete', 'force_delete_any',
        'replicate', 'reorder',
    ];

    /** Label Bahasa Indonesia untuk setiap aksi */
    public static array $actionLabels = [
        'view_any'         => 'Lihat Semua',
        'view'             => 'Lihat',
        'create'           => 'Buat',
        'update'           => 'Edit',
        'delete'           => 'Hapus',
        'delete_any'       => 'Hapus Semua',
        'restore'          => 'Pulihkan',
        'restore_any'      => 'Pulihkan Semua',
        'force_delete'     => 'Hapus Permanen',
        'force_delete_any' => 'Hapus P. Semua',
        'replicate'        => 'Duplikasi',
        'reorder'          => 'Urutkan',
    ];

    // ─────────────────────────────────────────
    // HELPERS: Parsing
    // ─────────────────────────────────────────

    /**
     * Parse nama permission → [action, resource].
     *
     * @param  bool $normalize  Jika true, normalisasi resource underscore → ::
     */
    public static function parsePermName(string $name, bool $normalize = true): array
    {
        if (str_starts_with($name, 'page_') || str_starts_with($name, 'widget_')) {
            return [str_starts_with($name, 'page_') ? 'page' : 'widget', $name];
        }

        $prefixes = [
            'force_delete_any_', 'force_delete_',
            'delete_any_', 'restore_any_', 'view_any_',
            'restore_', 'view_', 'create_', 'update_', 'delete_',
            'replicate_', 'reorder_',
        ];

        foreach ($prefixes as $prefix) {
            if (str_starts_with($name, $prefix)) {
                $action   = rtrim($prefix, '_');
                $resource = substr($name, strlen($prefix));
                if ($normalize) {
                    $resource = static::normalizeResourceSlug($resource);
                }
                return [$action, $resource];
            }
        }

        return [$name, '_custom'];
    }

    /** Cache normalisi resource slug */
    protected static array $resourceCanonicalCache = [];

    /**
     * Normalisasi slug underscore ke format :: jika ada padanannya di DB.
     * Contoh: "gift_card" → "gift::card" jika "gift::card" ada di DB.
     */
    public static function normalizeResourceSlug(string $resource): string
    {
        if (str_contains($resource, '::')) {
            return $resource;
        }

        if (isset(static::$resourceCanonicalCache[$resource])) {
            return static::$resourceCanonicalCache[$resource];
        }

        $parts = explode('_', $resource);
        $count = count($parts);

        for ($i = 1; $i < $count; $i++) {
            $candidate = implode('_', array_slice($parts, 0, $i))
                . '::'
                . implode('_', array_slice($parts, $i));

            $exists = Permission::where('name', 'like', "%_{$candidate}")
                ->orWhere('name', 'like', "{$candidate}_%")
                ->exists();

            if ($exists) {
                static::$resourceCanonicalCache[$resource] = $candidate;
                return $candidate;
            }
        }

        static::$resourceCanonicalCache[$resource] = $resource;
        return $resource;
    }

    /** Resource slug → label yang mudah dibaca */
    public static function resourceLabel(string $resource): string
    {
        return str(str_replace('::', ' ', $resource))->replace('_', ' ')->title()->toString();
    }

    /** Resource slug → key field form yang aman */
    public static function resourceKey(string $resource): string
    {
        return 'res_' . str_replace('::', '__', $resource);
    }

    // ─────────────────────────────────────────
    // HELPERS: Building Permission Map
    // ─────────────────────────────────────────

    /**
     * Ambil permission dari DB dan kelompokkan per resource,
     * dengan deduplication (format underscore di-skip jika ada :: equivalennya).
     *
     * @param  array  $keywords  Keyword filter — kosong = semua permission
     */
    public static function getResourcePermMap(array $keywords = []): array
    {
        $query = Permission::query();

        if (!empty($keywords)) {
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $q->orWhere('name', 'like', "%{$kw}%");
                }
            });
        }

        $permissions = $query->get();

        $mapWithColon    = [];
        $mapWithoutColon = [];

        foreach ($permissions as $perm) {
            [$action, $resource] = static::parsePermName($perm->name, normalize: false);

            if ($resource === '_custom'
                || str_starts_with($perm->name, 'page_')
                || str_starts_with($perm->name, 'widget_')
            ) {
                $mapWithColon[$resource][$action] = $perm->id;
                continue;
            }

            if (str_contains($resource, '::')) {
                $mapWithColon[$resource][$action] = $perm->id;
            } else {
                $mapWithoutColon[$resource][$action] = $perm->id;
            }
        }

        // Merge: skip underscore-resource jika punya padanan ::
        $map = $mapWithColon;

        foreach ($mapWithoutColon as $resource => $actions) {
            $parts = explode('_', $resource);
            $count = count($parts);
            $hasColonVersion = false;

            for ($i = 1; $i < $count; $i++) {
                $candidate = implode('_', array_slice($parts, 0, $i))
                    . '::'
                    . implode('_', array_slice($parts, $i));
                if (isset($mapWithColon[$candidate])) {
                    $hasColonVersion = true;
                    break;
                }
            }

            if (!$hasColonVersion) {
                $map[$resource] = $actions;
            }
        }

        ksort($map);
        return $map;
    }

    // ─────────────────────────────────────────
    // HELPERS: Building Filament Schema
    // ─────────────────────────────────────────

    /**
     * Bangun array schema Filament untuk satu kategori tab.
     * Setiap resource → Fieldset berisi primary + advanced CheckboxList.
     */
    protected static function buildCategorySchema(array $keywords): array
    {
        $schema      = [];
        $resourceMap = static::getResourcePermMap($keywords);

        foreach ($resourceMap as $resource => $actionMap) {
            // Skip special/custom — ditangani terpisah
            if ($resource === '_custom'
                || str_starts_with($resource, 'page_')
                || str_starts_with($resource, 'widget_')
            ) {
                continue;
            }

            $fieldKey     = static::resourceKey($resource);
            $resourceName = static::resourceLabel($resource);

            // Primary options
            $primaryOptions = [];
            foreach (static::$primaryActions as $action) {
                if (isset($actionMap[$action])) {
                    $primaryOptions[$actionMap[$action]] = static::$actionLabels[$action];
                }
            }

            // Advanced options
            $advancedOptions = [];
            foreach (static::$advancedActions as $action) {
                if (isset($actionMap[$action])) {
                    $advancedOptions[$actionMap[$action]] = static::$actionLabels[$action];
                }
            }

            $innerSchema = [];

            if (!empty($primaryOptions)) {
                $innerSchema[] = Forms\Components\CheckboxList::make($fieldKey)
                    ->label('')
                    ->options($primaryOptions)
                    ->columns(count($primaryOptions))
                    ->gridDirection('row')
                    ->bulkToggleable()
                    ->dehydrated(true);
            }

            if (!empty($advancedOptions)) {
                $innerSchema[] = Forms\Components\Section::make('Lanjutan')
                    ->description('Force delete, restore, replicate, reorder')
                    ->schema([
                        Forms\Components\CheckboxList::make($fieldKey . '__adv')
                            ->label('')
                            ->options($advancedOptions)
                            ->columns(count($advancedOptions))
                            ->gridDirection('row')
                            ->bulkToggleable()
                            ->dehydrated(true),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->compact();
            }

            if (!empty($innerSchema)) {
                $schema[] = Forms\Components\Fieldset::make($resourceName)
                    ->schema($innerSchema)
                    ->columnSpanFull();
            }
        }

        return $schema;
    }

    /**
     * Bangun schema khusus untuk tab "Khusus & Kasir".
     * Menampilkan custom permissions yang tidak punya prefix standar Shield,
     * seperti: process_refunds, open_cash_drawer, send_whatsapp_receipt, dll.
     */
    protected static function buildKhususSchema(): array
    {
        // Label ramah untuk setiap custom permission yang diketahui
        $customLabels = [
            'process_refunds'        => '🔄 Proses Refund',
            'open_cash_drawer'       => '🗄️ Buka Laci Kasir',
            'send_whatsapp_receipt'  => '📲 Kirim Nota WhatsApp',
            'adjust_ingredient_stock'=> '📦 Adjust Stok Bahan Baku',
            'apply_voucher'          => '🎟️ Pakai Voucher',
            'manage_email_marketing' => '📧 Kelola Email Marketing',
            'manage_feedback_rewards'=> '⭐ Kelola Reward Feedback',
            'manage_payment_settings'=> '💳 Kelola Pengaturan Pembayaran',
            'manage_whatsapp_settings'=> '📱 Kelola Pengaturan WhatsApp',
            'split_bill_by_item'     => '🧾 Split Bill per Item',
            'use_edc_payment'        => '💳 Gunakan Pembayaran EDC',
        ];


        // Ambil semua permission yang jatuh ke kategori _custom
        $customPerms = Permission::all()->filter(function ($perm) {
            if (str_starts_with($perm->name, 'page_') || str_starts_with($perm->name, 'widget_')) {
                return false;
            }

            $prefixes = [
                'force_delete_any_', 'force_delete_', 'delete_any_', 'restore_any_',
                'view_any_', 'restore_', 'view_', 'create_', 'update_', 'delete_',
                'replicate_', 'reorder_',
            ];

            foreach ($prefixes as $prefix) {
                if (str_starts_with($perm->name, $prefix)) {
                    return false;
                }
            }

            return true; // ini adalah custom permission
        });

        if ($customPerms->isEmpty()) {
            return [];
        }

        $options = $customPerms->mapWithKeys(function ($perm) use ($customLabels) {
            $label = $customLabels[$perm->name]
                ?? str($perm->name)->replace('_', ' ')->title()->toString();
            return [$perm->id => $label];
        })->toArray();

        return [
            Forms\Components\Fieldset::make('Aksi Khusus')
                ->schema([
                    Forms\Components\CheckboxList::make('res__custom')
                        ->label('')
                        ->options($options)
                        ->columns(['sm' => 2, 'lg' => 3])
                        ->gridDirection('row')
                        ->bulkToggleable()
                        ->dehydrated(true)
                        ->helperText('Permission khusus di luar resource standar. Berikan dengan hati-hati.'),
                ])
                ->columnSpanFull(),
        ];
    }

    /**
     * Bangun schema khusus untuk tab "Halaman & Widget" (page_ / widget_).
     */
    protected static function buildSistemSchema(): array
    {
        $pagePerms   = Permission::where('name', 'like', 'page_%')->get();
        $widgetPerms = Permission::where('name', 'like', 'widget_%')->get();

        $schema = [];

        if ($pagePerms->isNotEmpty()) {
            $schema[] = Forms\Components\Fieldset::make('Halaman (Pages)')
                ->schema([
                    Forms\Components\CheckboxList::make('res__pages')
                        ->label('')
                        ->options($pagePerms->mapWithKeys(fn ($p) => [
                            $p->id => str(str_replace('page_', '', $p->name))->headline()->toString(),
                        ])->sort()->toArray())
                        ->columns(['sm' => 2, 'lg' => 3])
                        ->gridDirection('row')
                        ->bulkToggleable()
                        ->dehydrated(true),
                ])
                ->columnSpanFull();
        }

        if ($widgetPerms->isNotEmpty()) {
            $schema[] = Forms\Components\Fieldset::make('Widget (Dashboard)')
                ->schema([
                    Forms\Components\CheckboxList::make('res__widgets')
                        ->label('')
                        ->options($widgetPerms->mapWithKeys(fn ($p) => [
                            $p->id => str(str_replace('widget_', '', $p->name))->headline()->toString(),
                        ])->sort()->toArray())
                        ->columns(['sm' => 2, 'lg' => 3])
                        ->gridDirection('row')
                        ->bulkToggleable()
                        ->dehydrated(true),
                ])
                ->columnSpanFull();
        }

        return $schema;
    }

    // ─────────────────────────────────────────
    // HELPERS: Mutate (fill & save)
    // ─────────────────────────────────────────

    /**
     * Distribusikan permission ID ke field res_* saat form dibuka.
     * Dipanggil dari mutateFormDataBeforeFill() di EditRole.
     */
    public static function distributePermissionsToFields(array $data, array $rolePermIds): array
    {
        $allPerms = Permission::all();

        $primarySet  = [];
        $advancedSet = [];

        foreach ($allPerms as $perm) {
            [$action, $resource] = static::parsePermName($perm->name, normalize: false);

            if (str_starts_with($perm->name, 'page_')) {
                $primarySet['res__pages'][] = $perm->id;
                continue;
            }
            if (str_starts_with($perm->name, 'widget_')) {
                $primarySet['res__widgets'][] = $perm->id;
                continue;
            }
            if ($resource === '_custom') {
                // Custom permission → masukkan ke field res__custom
                $primarySet['res__custom'][] = $perm->id;
                continue;
            }


            // Normalisasi: cari apakah ini versi underscore dari :: resource
            $normalized = static::normalizeResourceSlug($resource);
            $fieldKey   = static::resourceKey(
                str_contains($resource, '::') ? $resource : $normalized
            );

            if (in_array($action, static::$primaryActions)) {
                $primarySet[$fieldKey][] = $perm->id;
            } elseif (in_array($action, static::$advancedActions)) {
                $advancedSet[$fieldKey . '__adv'][] = $perm->id;
            }
        }

        foreach ($primarySet as $fieldKey => $allIds) {
            $data[$fieldKey] = array_values(array_intersect($rolePermIds, $allIds));
        }
        foreach ($advancedSet as $fieldKey => $allIds) {
            $data[$fieldKey] = array_values(array_intersect($rolePermIds, $allIds));
        }

        return $data;
    }

    /**
     * Kumpulkan semua nilai res_* dari form data untuk disimpan.
     * Dipanggil dari mutateFormDataBeforeSave() di EditRole.
     */
    public static function collectPermissionsFromFields(array &$data): array
    {
        $selectedIds = [];

        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'res_')) {
                $selectedIds = array_merge($selectedIds, (array) $value);
                unset($data[$key]);
            }
        }

        return array_unique($selectedIds);
    }
}
