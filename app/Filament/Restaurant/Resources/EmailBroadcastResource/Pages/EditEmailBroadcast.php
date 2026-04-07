<?php

namespace App\Filament\Restaurant\Resources\EmailBroadcastResource\Pages;

use App\Filament\Restaurant\Resources\EmailBroadcastResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmailBroadcast extends EditRecord
{
    protected static string $resource = EmailBroadcastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
