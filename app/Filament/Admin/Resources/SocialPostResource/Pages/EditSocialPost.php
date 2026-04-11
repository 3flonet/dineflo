<?php

namespace App\Filament\Admin\Resources\SocialPostResource\Pages;

use App\Filament\Admin\Resources\SocialPostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSocialPost extends EditRecord
{
    protected static string $resource = SocialPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
