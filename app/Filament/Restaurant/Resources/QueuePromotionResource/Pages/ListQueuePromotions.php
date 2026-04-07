<?php

namespace App\Filament\Restaurant\Resources\QueuePromotionResource\Pages;

use App\Filament\Restaurant\Resources\QueuePromotionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQueuePromotions extends ListRecords
{
    protected static string $resource = QueuePromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('set_running_text')
                ->label('Set Running Text')
                ->icon('heroicon-m-chat-bubble-bottom-center-text')
                ->color('info')
                ->visible(fn () => auth()->user()->can('update_queue::promotion'))
                ->modalHeading('Pengaturan Running Text Antrean')
                ->modalDescription('Teks berjalan yang akan muncul di bagian bawah layar TV antrean.')
                ->form([
                    \Filament\Forms\Components\Textarea::make('queue_display_running_text')
                        ->label('Pesan Running Text')
                        ->placeholder('Cth: Promo Hari Ini! Beli 1 Gratis 1 Menu Ayam Bakar... | Selamat Menikmati Hidangan Kami!')
                        ->rows(4)
                        ->helperText('Gunakan tanda | sebagai pemisah jika ingin menampilkan beberapa pesan bergantian.'),
                ])
                ->fillForm(fn () => [
                    'queue_display_running_text' => \Filament\Facades\Filament::getTenant()->queue_display_running_text,
                ])
                ->action(function (array $data) {
                    $restaurant = \Filament\Facades\Filament::getTenant();
                    $restaurant->update([
                        'queue_display_running_text' => $data['queue_display_running_text'],
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Berhasil!')
                        ->body('Running text antrean telah diperbarui.')
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make()
                ->label('Tambah Konten Promo'),
        ];
    }
}
