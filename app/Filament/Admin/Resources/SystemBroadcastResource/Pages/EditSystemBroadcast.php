<?php

namespace App\Filament\Admin\Resources\SystemBroadcastResource\Pages;

use App\Filament\Admin\Resources\SystemBroadcastResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSystemBroadcast extends EditRecord
{
    protected static string $resource = SystemBroadcastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
