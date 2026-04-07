<?php

namespace App\Filament\Restaurant\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Database\Eloquent\Model;

class RegisterRestaurant extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register New Branch';
    }

    public static function getSlug(): string
    {
        return 'setup';
    }

    public function mount(): void
    {
        $user = auth()->user();

        // Check if user is eligible to register a restaurant
        if (!$user->hasRole('super_admin')) {
            // If they already have restaurants, they need Multi-Restaurant Support
            if ($user->ownedRestaurants()->exists()) {
                if (!$user->hasFeature('Multi-Restaurant Support')) {
                    \Filament\Notifications\Notification::make()
                        ->title('Access Denied')
                        ->body('Paket Anda tidak mendukung pembuatan cabang tambahan. Silakan upgrade paket Anda di HQ Panel.')
                        ->danger()
                        ->send();
                    
                    $this->redirect('/restaurants');
                    return;
                }
            }
            
            // Basic plan check (limit check)
            if (!$user->canCreateRestaurant()) {
                \Filament\Notifications\Notification::make()
                    ->title('Limit Reached')
                    ->body('Anda telah mencapai batas maksimum restoran untuk paket Anda.')
                    ->danger()
                    ->send();
                
                $this->redirect('/restaurants');
                return;
            }
        }

        $this->form->fill([
            'name' => request('name') ?? session('onboarding_restaurant_name'),
            'slug' => request('slug') ?? session('onboarding_restaurant_slug'),
        ]);

        // Keep session for one more flash if needed, or clear it if filled
        if (request('name') || session('onboarding_restaurant_name')) {
            session()->forget(['onboarding_restaurant_name', 'onboarding_restaurant_slug']);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                
                TextInput::make('slug')
                    ->required()
                    ->unique('restaurants', 'slug')
                    ->maxLength(255),
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        $user = auth()->user();

        if (!$user->canCreateRestaurant()) {
            \Filament\Notifications\Notification::make()
                ->title('Limit Reached')
                ->body('You have reached the maximum number of restaurants allowed by your plan. Please upgrade to add more.')
                ->danger()
                ->send();
            
            $this->halt();
        }

        // Add current user as owner
        $data['user_id'] = $user->id;

        // Set default opening hours
        $data['opening_hours'] = [
            ['day' => 'monday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            ['day' => 'tuesday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            ['day' => 'wednesday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            ['day' => 'thursday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            ['day' => 'friday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            ['day' => 'saturday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            ['day' => 'sunday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
        ];
        
        $restaurant = \App\Models\Restaurant::create($data);

        // Assign 'restaurant_owner' role for this tenant
        setPermissionsTeamId($restaurant->id);
        $user->assignRole('restaurant_owner');

        return $restaurant;
    }
}
