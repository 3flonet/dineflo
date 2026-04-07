<?php

namespace App\Filament\Restaurant\Resources\FinanceResource\Pages;

use App\Filament\Restaurant\Resources\FinanceResource;
use Filament\Resources\Pages\ListRecords;

class ListFinances extends ListRecords
{
    protected static string $resource = FinanceResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Bisa tambah widget statistik di sini nanti
        ];
    }
}
