<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter Form --}}
        <x-filament::section>
            <x-slot name="heading">
                Filter Laporan
            </x-slot>
            
            <form wire:submit.prevent="applyFilter">
                {{ $this->form }}
                
                <div class="mt-4 flex justify-end">
                    <x-filament::button type="submit">
                        Terapkan Filter
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        {{-- Widgets --}}
        @livewire(\App\Filament\Restaurant\Widgets\ReportStatsWidget::class)
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
             @livewire(\App\Filament\Restaurant\Widgets\ReportChartWidget::class)
             @livewire(\App\Filament\Restaurant\Widgets\PeakHoursChartWidget::class)
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
             @livewire(\App\Filament\Restaurant\Widgets\TopSellingItemsWidget::class)
             @livewire(\App\Filament\Restaurant\Widgets\TopCustomersWidget::class)
        </div>
    </div>
</x-filament-panels::page>
