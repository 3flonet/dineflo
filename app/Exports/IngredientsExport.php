<?php

namespace App\Exports;

use App\Models\Ingredient;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Filament\Facades\Filament;

class IngredientsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Ingredient::where('restaurant_id', Filament::getTenant()->id)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Unit',
            'Cost Per Unit',
            'Current Stock',
            'Min Stock Alert',
            'Bulk Purchase Price',
            'Bulk Quantity',
            'Purchase Unit',
            'Bulk Unit'
        ];
    }

    public function map($ingredient): array
    {
        return [
            $ingredient->id,
            $ingredient->name,
            $ingredient->unit,
            $ingredient->cost_per_unit,
            $ingredient->stock,
            $ingredient->min_stock_alert,
            $ingredient->bulk_purchase_price,
            $ingredient->bulk_quantity,
            $ingredient->purchase_unit,
            $ingredient->bulk_unit_type,
        ];
    }
}
