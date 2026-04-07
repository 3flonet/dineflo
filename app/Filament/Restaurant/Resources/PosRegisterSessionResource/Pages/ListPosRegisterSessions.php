<?php

namespace App\Filament\Restaurant\Resources\PosRegisterSessionResource\Pages;

use App\Filament\Restaurant\Resources\PosRegisterSessionResource;
use Filament\Resources\Pages\ListRecords;

class ListPosRegisterSessions extends ListRecords
{
    protected static string $resource = PosRegisterSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
