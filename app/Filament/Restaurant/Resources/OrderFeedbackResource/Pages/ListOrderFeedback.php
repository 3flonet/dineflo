<?php

namespace App\Filament\Restaurant\Resources\OrderFeedbackResource\Pages;

use App\Filament\Restaurant\Resources\OrderFeedbackResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrderFeedback extends ListRecords
{
    protected static string $resource = OrderFeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
