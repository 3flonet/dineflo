<?php

namespace App\Filament\Admin\Resources\SystemBroadcastResource\Pages;

use App\Filament\Admin\Resources\SystemBroadcastResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSystemBroadcasts extends ListRecords
{
    protected static string $resource = SystemBroadcastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
