<?php

namespace App\Filament\Admin\Resources\WithdrawRequestResource\Pages;

use App\Filament\Admin\Resources\WithdrawRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListWithdrawRequests extends ListRecords
{
    protected static string $resource = WithdrawRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->exports([
                    ExcelExport::make('export_withdraw_requests')->fromTable(),
                ])
                ->icon('heroicon-o-document-arrow-down'),
            Actions\CreateAction::make(),
        ];
    }
}
