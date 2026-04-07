<?php

namespace App\Filament\Admin\Resources\RoleResource\Pages;

use App\Filament\Admin\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    /**
     * Distribusikan permission ID ke field res_* per resource
     * agar setiap Fieldset + CheckboxList terisi dengan benar saat form dibuka.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $rolePermIds = $this->record->permissions->pluck('id')->toArray();
        return RoleResource::distributePermissionsToFields($data, $rolePermIds);
    }

    /**
     * Kumpulkan semua field res_* dari form, lalu sync ke tabel pivot permission.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $selectedIds = RoleResource::collectPermissionsFromFields($data);
        $this->record->permissions()->sync($selectedIds);
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(fn () => in_array($this->record->name, RoleResource::$systemRoles)),
        ];
    }
}
