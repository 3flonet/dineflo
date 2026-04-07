<?php

namespace App\Filament\Restaurant\Resources\CashDrawerLogResource\Pages;

use App\Filament\Restaurant\Resources\CashDrawerLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashDrawerLogs extends ListRecords
{
    protected static string $resource = CashDrawerLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
