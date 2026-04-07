<?php

namespace App\Filament\Restaurant\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;

class Register extends BaseRegister
{
    protected static string $view = 'filament.restaurant.pages.auth.register';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                TextInput::make('phone')
                    ->label('Nomor WhatsApp')
                    ->tel()
                    ->placeholder('08123456789')
                    ->helperText(function() {
                        try {
                            $siteName = app(\App\Settings\GeneralSettings::class)->site_name;
                        } catch (\Throwable $e) {
                            $siteName = config('app.name', 'Dineflo');
                        }
                        return "Nomor ini akan digunakan untuk menerima notifikasi sistem dari {$siteName}.";
                    })
                    ->maxLength(20),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        $user = $this->getUserModel()::create($data);
        
        // Assign default role for self-registered users: 'restaurant_owner'
        try {
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('restaurant_owner');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Role assignment failed: ' . $e->getMessage());
        }

        return $user;
    }
}
