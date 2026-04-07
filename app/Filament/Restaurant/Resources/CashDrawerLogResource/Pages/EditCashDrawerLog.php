<?php

namespace App\Filament\Restaurant\Resources\CashDrawerLogResource\Pages;

use App\Filament\Restaurant\Resources\CashDrawerLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashDrawerLog extends EditRecord
{
    protected static string $resource = CashDrawerLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
