<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;

class SystemGuide extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Panduan Sistem';

    protected static ?string $title = 'Pusat Panduan Admin';

    protected static ?string $navigationGroup = 'Sistem & Pengaturan';

    protected static string $view = 'filament.admin.pages.system-guide';

    protected static ?int $navigationSort = 100;

    public function getHeaderWidgets(): array
    {
        return [];
    }
}
