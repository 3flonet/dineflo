<?php

namespace App\Filament\Restaurant\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Filament\Facades\Filament;

class MyProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $title = 'Profil Saya';
    protected static ?string $navigationLabel = 'Profil Saya';
    protected static ?string $navigationGroup = 'PENGATURAN TOKO';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.restaurant.pages.my-profile';

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();
        
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'notification_preferences' => $user->notification_preferences ?? $this->getDefaultPreferences(),
        ]);
    }

    protected function getDefaultPreferences(): array
    {
        return [
            'order_new' => ['database', 'push', 'sound'],
            'waiter_call' => ['database', 'push', 'sound'],
            'reservation_new' => ['database', 'push'],
            'withdraw_status' => ['database', 'push'],
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pribadi')
                    ->description('Perbarui informasi profil dan alamat email Anda.')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->label('Alamat Email')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Nomor WhatsApp')
                                    ->tel()
                                    ->helperText('Gunakan format internasional (cth: 62812...)'),
                            ]),
                    ]),

                Forms\Components\Section::make('Preferensi Notifikasi & Suara')
                    ->description('Atur bagaimana Anda ingin menerima pemberitahuan sistem.')
                    ->schema([
                        Forms\Components\CheckboxList::make('notification_preferences.order_new')
                            ->label('Pesanan Baru (New Order)')
                            ->options([
                                'database' => 'Notifikasi Panel (Lonceng)',
                                'push' => 'Push Notification (Browser/PWA)',
                                'sound' => 'Sound Alert (Suara Ding-dong)',
                            ])
                            ->columns(3)
                            ->gridDirection('row'),

                        Forms\Components\CheckboxList::make('notification_preferences.waiter_call')
                            ->label('Panggilan Waiter (Waiter Call)')
                            ->options([
                                'database' => 'Notifikasi Panel (Lonceng)',
                                'push' => 'Push Notification (Browser/PWA)',
                                'sound' => 'Sound Alert (Suara Bell)',
                            ])
                            ->columns(3)
                            ->gridDirection('row'),

                        Forms\Components\CheckboxList::make('notification_preferences.reservation_new')
                            ->label('Reservasi Baru')
                            ->options([
                                'database' => 'Notifikasi Panel (Lonceng)',
                                'push' => 'Push Notification (Browser/PWA)',
                            ])
                            ->columns(3),

                        Forms\Components\CheckboxList::make('notification_preferences.withdraw_status')
                            ->label('Update Status Withdraw')
                            ->options([
                                'database' => 'Notifikasi Panel (Lonceng)',
                                'push' => 'Push Notification (Browser/PWA)',
                            ])
                            ->columns(3),
                    ]),

                Forms\Components\Section::make('Keamanan')
                    ->description('Kosongkan jika tidak ingin mengubah kata sandi.')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('password')
                                    ->label('Kata Sandi Baru')
                                    ->password()
                                    ->revealable()
                                    ->minLength(8)
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->currentPassword(false),
                                Forms\Components\TextInput::make('password_confirmation')
                                    ->label('Konfirmasi Kata Sandi Baru')
                                    ->password()
                                    ->revealable()
                                    ->requiredWith('password')
                                    ->same('password')
                                    ->dehydrated(false),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        Notification::make()
            ->title('Profil Berhasil Diperbarui')
            ->success()
            ->send();
            
        // Jika password diubah, arahkan ke login? Biasanya tidak perlu di Filament
    }
}
