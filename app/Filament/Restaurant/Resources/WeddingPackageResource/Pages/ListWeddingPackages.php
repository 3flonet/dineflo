<?php

namespace App\Filament\Restaurant\Resources\WeddingPackageResource\Pages;

use App\Filament\Restaurant\Resources\WeddingPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWeddingPackages extends ListRecords
{
    protected static string $resource = WeddingPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
