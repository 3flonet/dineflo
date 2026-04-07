<x-filament-panels::page>
    <x-filament-panels::form wire:submit="submit">
        {{ $this->form }}

        <div class="flex items-center gap-4 mt-6">
            <x-filament::button type="submit" size="lg">
                Simpan Perubahan
            </x-filament::button>
            <x-filament::button color="gray" tag="a" href="{{ \Filament\Facades\Filament::getTenant() ? route('filament.restaurant.pages.dashboard', ['tenant' => \Filament\Facades\Filament::getTenant()->slug]) : route('filament.restaurant.pages.dashboard') }}">
                Batal
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
