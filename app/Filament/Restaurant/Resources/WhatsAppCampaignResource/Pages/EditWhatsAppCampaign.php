<?php

namespace App\Filament\Restaurant\Resources\WhatsAppCampaignResource\Pages;

use App\Filament\Restaurant\Resources\WhatsAppCampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWhatsAppCampaign extends EditRecord
{
    protected static string $resource = WhatsAppCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
