<?php

namespace App\Filament\Restaurant\Resources\WaiterCallResource\Pages;

use App\Filament\Restaurant\Resources\WaiterCallResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWaiterCall extends EditRecord
{
    protected static string $resource = WaiterCallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
