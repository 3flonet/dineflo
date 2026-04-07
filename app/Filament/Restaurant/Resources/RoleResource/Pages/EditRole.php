<?php

namespace App\Filament\Restaurant\Resources\RoleResource\Pages;

use App\Filament\Restaurant\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $rolePermIds = $this->record->permissions->pluck('id')->toArray();
        return RoleResource::distributePermissionsToFields($data, $rolePermIds);
    }

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
