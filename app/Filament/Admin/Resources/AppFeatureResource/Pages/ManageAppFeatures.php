<?php

namespace App\Filament\Admin\Resources\AppFeatureResource\Pages;

use App\Filament\Admin\Resources\AppFeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAppFeatures extends ManageRecords
{
    protected static string $resource = AppFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
