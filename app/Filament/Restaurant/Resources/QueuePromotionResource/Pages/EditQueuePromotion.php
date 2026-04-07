<?php

namespace App\Filament\Restaurant\Resources\QueuePromotionResource\Pages;

use App\Filament\Restaurant\Resources\QueuePromotionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQueuePromotion extends EditRecord
{
    protected static string $resource = QueuePromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
