<?php

namespace App\Filament\Restaurant\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Actions\Action;

class Reports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.restaurant.pages.reports';
    
    protected static ?string $navigationLabel = 'Laporan Penjualan';

    protected static ?string $title = 'Laporan Penjualan';

    protected static ?string $navigationGroup = 'KEUANGAN';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        // Require Shield check first, then Feature check
        return auth()->user()->can('page_Reports') && auth()->user()->hasFeature('Sales Reports');
    }

    public ?array $data = [];

    public function mount(): void
    {
        // Feature Gating: Check 'Sales Reports'
        if (! auth()->user()->hasFeature('Sales Reports')) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Anda memerlukan paket Pro untuk mengakses fitur Laporan.')
                ->danger()
                ->send();
            
            $this->redirect(route('filament.restaurant.pages.dashboard', ['tenant' => \Filament\Facades\Filament::getTenant()]));
            return;
        }

        $this->form->fill([
            'date_start' => request('date_start', now()->startOfMonth()->format('Y-m-d')),
            'date_end'   => request('date_end', now()->endOfMonth()->format('Y-m-d')),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filter Periode')
                    ->schema([
                        DatePicker::make('date_start')
                            ->label('Dari Tanggal')
                            ->required(),
                        DatePicker::make('date_end')
                            ->label('Sampai Tanggal')
                            ->required(),
                    ])
                    ->columns(2)
            ])
            ->statePath('data');
    }

    public function applyFilter()
    {
        $data = $this->form->getState();

        return redirect()->route('filament.restaurant.pages.reports', [
            'tenant' => \Filament\Facades\Filament::getTenant(),
            'date_start' => $data['date_start'],
            'date_end' => $data['date_end'],
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_csv')
                ->label('Export CSV')
                ->color('success')
                ->url(fn () => route('reports.export', [
                    'restaurant' => \Filament\Facades\Filament::getTenant(),
                    'date_start' => $this->data['date_start'] ?? now()->format('Y-m-d'), 
                    'date_end' => $this->data['date_end'] ?? now()->format('Y-m-d'),
                ]))
                ->openUrlInNewTab(),
                
            Action::make('export_pdf')
                ->label('Export PDF')
                ->color('danger')
                ->icon('heroicon-o-document-arrow-down')
                ->url(fn () => route('reports.export_pdf', [
                    'restaurant' => \Filament\Facades\Filament::getTenant(),
                    'date_start' => $this->data['date_start'] ?? now()->format('Y-m-d'), 
                    'date_end' => $this->data['date_end'] ?? now()->format('Y-m-d'),
                ]))
                ->openUrlInNewTab(),

            Action::make('export_excel')
                ->label('Export Excel')
                ->color('success')
                ->icon('heroicon-o-table-cells')
                ->url(fn () => route('reports.export_excel', [
                    'restaurant' => \Filament\Facades\Filament::getTenant(),
                    'date_start' => $this->data['date_start'] ?? now()->format('Y-m-d'), 
                    'date_end' => $this->data['date_end'] ?? now()->format('Y-m-d'),
                ]))
                ->openUrlInNewTab(),
        ];
    }
    
    // Header widgets removed because we render them manually in blade
    public function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getHeaderWidgetsConfig(): array
    {
        return [
            'filters' => [
                'startDate' => $this->data['date_start'] ?? now()->startOfMonth()->format('Y-m-d'),
                'endDate' => $this->data['date_end'] ?? now()->endOfMonth()->format('Y-m-d'),
            ],
        ];
    }
}
