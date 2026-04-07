<?php

namespace App\Filament\Restaurant\Resources\GiftCardResource\Pages;

use App\Filament\Restaurant\Pages\DistributeGiftCard;
use App\Filament\Restaurant\Resources\GiftCardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGiftCards extends ListRecords
{
    protected static string $resource = GiftCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('distribute')
                ->label('Distribusi Gift Card')
                ->icon('heroicon-o-paper-airplane')
                ->color('gray')
                ->url(fn () => DistributeGiftCard::getUrl()),
            Actions\CreateAction::make()
                ->label('Buat Gift Card'),
        ];
    }
}
