<?php

namespace App\Filament\Restaurant\Resources\GiftCardResource\Pages;

use App\Filament\Restaurant\Resources\GiftCardResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewGiftCard extends ViewRecord
{
    protected static string $resource = GiftCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('send_notification')
                ->label('Kirim Notifikasi')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->visible(fn () => $this->record->status === 'active' && ($this->record->recipient_phone || $this->record->recipient_email))
                ->action(function () {
                    $restaurant = Filament::getTenant();
                    $result = GiftCardResource::dispatchGiftCardNotifications($this->record, $restaurant);

                    if (empty($result['sent'])) {
                        Notification::make()
                            ->warning()
                            ->title('Notifikasi tidak terkirim')
                            ->body($result['reason'] ?? 'Tidak ada channel pengiriman yang aktif.')
                            ->send();
                    } else {
                        Notification::make()
                            ->success()
                            ->title('Notifikasi berhasil dikirim!')
                            ->body('Terkirim via: ' . implode(' & ', $result['sent']))
                            ->send();
                    }
                }),
            Actions\EditAction::make(),
        ];
    }
}
