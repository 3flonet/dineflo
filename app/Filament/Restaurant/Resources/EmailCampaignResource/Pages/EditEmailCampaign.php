<?php

namespace App\Filament\Restaurant\Resources\EmailCampaignResource\Pages;

use App\Filament\Restaurant\Resources\EmailCampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmailCampaign extends EditRecord
{
    protected static string $resource = EmailCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sendTest')
                ->label('Uji Coba Kirim')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\TextInput::make('recipient_email')
                        ->label('Email Penerima')
                        ->email()
                        ->default(fn () => auth()->user()->email)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    try {
                        app(\App\Services\MarketingMailService::class)->sendTestEmail($this->record, $data['recipient_email']);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Email Uji Coba Terkirim')
                            ->body('Email kampanye sedang dikirim ke ' . $data['recipient_email'])
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Gagal Mengirim Email')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
