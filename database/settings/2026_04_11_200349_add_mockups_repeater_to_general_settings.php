<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.landing_hero_mockups', [
            ['title' => 'FILAMENT V3', 'image' => null],
            ['title' => 'LIVEWIRE 3', 'image' => null],
            ['title' => 'MIDTRANS', 'image' => null],
            ['title' => 'LARAVEL REVERB', 'image' => null],
        ]);
    }

    public function down(): void
    {
        $this->migrator->delete('general.landing_hero_mockups');
    }
};
