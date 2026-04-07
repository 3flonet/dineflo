<?php

namespace App\Filament\Restaurant\Resources\MenuItemResource\Pages;

use App\Filament\Restaurant\Resources\MenuItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMenuItem extends EditRecord
{
    protected static string $resource = MenuItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Check apakah item punya variants
        $data['has_variants'] = !empty($data['variants']);
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Update Base Price dari variant terendah
        if (!empty($data['variants'])) {
            $prices = collect($data['variants'])->pluck('price')->filter()->toArray();
            if (count($prices) > 0) {
                $data['price'] = min($prices);
            }
        } elseif (isset($data['has_variants']) && !$data['has_variants']) {
            // Jika variants dimatikan, pastikan variants kosong
            $data['variants'] = [];
        }

        unset($data['has_variants']);

        return $data;
    }

    protected function afterSave(): void
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
