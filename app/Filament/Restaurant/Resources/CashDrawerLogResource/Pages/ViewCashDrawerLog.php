<?php

namespace App\Filament\Restaurant\Resources\CashDrawerLogResource\Pages;

use App\Filament\Restaurant\Resources\CashDrawerLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCashDrawerLog extends ViewRecord
{
    protected static string $resource = CashDrawerLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
