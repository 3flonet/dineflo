<?php

namespace App\Filament\Admin\Resources\RoleResource\Pages;

use App\Filament\Admin\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected array $permissionData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $selected = [];
        
        foreach (array_keys(RoleResource::$permissionCategories) as $key) {
            $selected = array_merge($selected, $data["permissions_{$key}"] ?? []);
            unset($data["permissions_{$key}"]);
        }
        
        $this->permissionData = array_unique($selected);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->permissions()->sync($this->permissionData);
    }
}
