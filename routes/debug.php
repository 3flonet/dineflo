<?php

// ⚠️  FILE INI HANYA DI-LOAD SAAT app()->isLocal() — lihat web.php
// JANGAN pernah memanggil Auth::login() di sini tanpa verifikasi password.

Route::middleware(['web', 'auth'])->group(function () {

    Route::get('/debug-nav', function () {
        $user   = auth()->user(); // Gunakan user yang sudah login, bukan force login
        $tenant = \Filament\Facades\Filament::getTenant();

        $out  = 'Tenant: ' . ($tenant ? $tenant->slug : 'NULL') . "\n";
        $out .= 'User: '   . ($user  ? $user->email   : 'NULL') . "\n";
        $out .= 'Roles: '  . ($user  ? $user->roles->pluck('name')->join(',') : 'NULL') . "\n";
        $out .= 'can(view_any_order): '         . ($user && $user->can('view_any_order') ? 'YES' : 'NO') . "\n";
        $out .= 'can(view_any_menu::category): ' . ($user && $user->can('view_any_menu::category') ? 'YES' : 'NO') . "\n";
        $out .= 'can(view_any_user): '           . ($user && $user->can('view_any_user') ? 'YES' : 'NO') . "\n";

        try {
            $panel = \Filament\Facades\Filament::getPanel('restaurant');
            $nav   = $panel->getNavigation();

            foreach ($nav as $group) {
                $out .= 'Group: ' . $group->getLabel() . "\n";
                foreach ($group->getItems() as $item) {
                    $visible = $item->isVisible() ? 'YES' : 'NO';
                    $out .= '- ' . $item->getLabel() . " [Visible: $visible]\n";
                }
            }
        } catch (\Throwable $e) {
            $out .= 'Nav Error: ' . $e->getMessage() . "\n";
        }

        return "<pre>$out</pre>";
    });

});
