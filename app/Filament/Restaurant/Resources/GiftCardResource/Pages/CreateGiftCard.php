<?php

namespace App\Filament\Restaurant\Resources\GiftCardResource\Pages;

use App\Filament\Restaurant\Resources\GiftCardResource;
use App\Models\GiftCard;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateGiftCard extends CreateRecord
{
    protected static string $resource = GiftCardResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['code']              = GiftCard::generateCode();
        $data['remaining_balance'] = $data['original_amount'];
        $data['created_by']        = auth()->id();
        $data['restaurant_id']     = Filament::getTenant()->id;

        return $data;
    }

    protected function afterCreate(): void
    {
        $restaurant = Filament::getTenant();
        $record     = $this->record;

        // Smart dispatch: kirim via semua channel yang tersedia & valid
        $result = GiftCardResource::dispatchGiftCardNotifications($record, $restaurant);

        if (!empty($result['sent'])) {
            Notification::make()
                ->success()
                ->title('Gift Card dibuat & dikirim! 🎁')
                ->body("Kode {$record->code} terkirim ke {$record->recipient_name} via " . implode(' & ', $result['sent']) . '.')
                ->send();
        } else {
            // Kartu tetap berhasil dibuat, meskipun tidak ada channel
            Notification::make()
                ->success()
                ->title("Gift Card {$record->code} berhasil dibuat!")
                ->body($result['reason'] ?? 'Tidak ada channel notifikasi yang aktif.')
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
