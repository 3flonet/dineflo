<?php

namespace App\Filament\Admin\Resources\SocialPostResource\Pages;

use App\Filament\Admin\Resources\SocialPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSocialPosts extends ListRecords
{
    protected static string $resource = SocialPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
