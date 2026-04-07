<?php

namespace App\Filament\Restaurant\Resources\EmailBroadcastResource\Pages;

use App\Filament\Restaurant\Resources\EmailBroadcastResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmailBroadcasts extends ListRecords
{
    protected static string $resource = EmailBroadcastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
