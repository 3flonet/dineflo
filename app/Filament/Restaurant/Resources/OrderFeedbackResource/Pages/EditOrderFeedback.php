<?php

namespace App\Filament\Restaurant\Resources\OrderFeedbackResource\Pages;

use App\Filament\Restaurant\Resources\OrderFeedbackResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrderFeedback extends EditRecord
{
    protected static string $resource = OrderFeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSaveFormAction(): Actions\Action
    {
        return parent::getSaveFormAction()
            ->label('Simpan & Kirim Balasan');
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        $restaurant = $record->restaurant;
        $order = $record->order;

        if (!$record->reply_comment || !$order) {
            return;
        }

        $customerName = $order->customer_name ?: 'Pelanggan';
        $customerPhone = $order->customer_phone;
        $customerEmail = $order->customer_email;

        // 1. WhatsApp Notification
        if ($restaurant->wa_is_active && $customerPhone) {
            $waMessage = "Halo Kak *{$customerName}*,\n\n";
            $waMessage .= "Terima kasih atas ulasan Anda untuk *{$restaurant->name}*.\n\n";
            $waMessage .= "*Balasan Kami:*\n";
            $waMessage .= "_{$record->reply_comment}_\n\n";
            $waMessage .= "Sampai jumpa kembali di kunjungan berikutnya! 🙏";

            \App\Services\WhatsApp\WhatsAppService::sendMessage($restaurant, $customerPhone, $waMessage);
        }

        // 2. Email Notification
        if ($customerEmail) {
            \App\Jobs\SendWhitelabelMail::dispatch(
                $restaurant,
                $customerEmail,
                new \App\Mail\FeedbackReplyMail($restaurant, $record, $record->reply_comment, $customerName)
            );
        }

        \Filament\Notifications\Notification::make()
            ->title('Balasan Terkirim')
            ->body('Tanggapan telah disimpan dan dikirim ke pelanggan via WhatsApp/Email.')
            ->success()
            ->send();
    }
}
