<?php

namespace App\Filament\Restaurant\Resources\MenuItemResource\Pages;

use App\Filament\Restaurant\Resources\MenuItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMenuItem extends CreateRecord
{
    protected static string $resource = MenuItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Jika ada variants, ambil harga terendah sebagai Base Price
        if (!empty($data['variants'])) {
            $prices = collect($data['variants'])->pluck('price')->filter()->toArray();
            if (count($prices) > 0) {
                $data['price'] = min($prices);
            }
        }
        
        unset($data['has_variants']);

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->record->is_reciprocal) {
            $this->record->load('upsells');
            $currentId = $this->record->id;

            foreach ($this->record->upsells as $upsell) {
                $targetId = $upsell->upsell_item_id;
                if ($targetId) {
                    \App\Models\MenuItemUpsell::updateOrCreate([
                        'menu_item_id' => $targetId,
                        'upsell_item_id' => $currentId,
                    ]);
                }
            }
        }
    }
}
