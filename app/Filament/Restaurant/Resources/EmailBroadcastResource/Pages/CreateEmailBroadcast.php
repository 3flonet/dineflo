<?php

namespace App\Filament\Restaurant\Resources\EmailBroadcastResource\Pages;

use App\Filament\Restaurant\Resources\EmailBroadcastResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEmailBroadcast extends CreateRecord
{
    protected static string $resource = EmailBroadcastResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['trigger_type'] = 'manual';
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
