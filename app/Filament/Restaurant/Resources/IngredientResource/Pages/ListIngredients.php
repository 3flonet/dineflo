<?php

namespace App\Filament\Restaurant\Resources\IngredientResource\Pages;

use App\Filament\Restaurant\Resources\IngredientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIngredients extends ListRecords
{
    protected static string $resource = IngredientResource::class;

    protected $listeners = ['refresh-ingredients-list' => '$refresh'];

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn () => \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\IngredientsExport, 'ingredients.xlsx')),

            Actions\Action::make('import')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('Pilih File Excel')
                        ->disk('local')
                        ->required()
                        ->helperText('Gunakan format kolom: name, unit, cost_per_unit, current_stock, min_stock_alert'),
                ])
                ->action(function (array $data) {
                    \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\IngredientsImport, storage_path('app/' . $data['file']));
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Import Berhasil')
                        ->body('Data bahan baku telah diperbarui.')
                        ->success()
                        ->send();
                }),

            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Restaurant\Resources\IngredientResource\Widgets\LowStockIngredientWidget::class,
        ];
    }
}
