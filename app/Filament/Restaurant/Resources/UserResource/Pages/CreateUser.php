<?php

namespace App\Filament\Restaurant\Resources\UserResource\Pages;

use App\Filament\Restaurant\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Automatically associate the user with the current tenant
        $data['restaurant_id'] = \Filament\Facades\Filament::getTenant()->id;
        
        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        \Illuminate\Support\Facades\Log::info('CreateUser: Handle Record Creation started', ['data_keys' => array_keys($data)]);
        
        $roleIds = $data['roles'] ?? [];
        unset($data['roles']); // Clean for create

        $record = static::getModel()::create($data);

        // Fetch Role Models explicitly to ensure Spatie receives valid objects
        try {
            if (!empty($roleIds)) {
                \Illuminate\Support\Facades\Log::info('CreateUser: Fetching roles by IDs...', ['ids' => $roleIds]);
                $roles = \App\Models\Role::whereIn('id', $roleIds)->get();
                
                \Illuminate\Support\Facades\Log::info('CreateUser: Roles found.', ['count' => $roles->count(), 'roles' => $roles->pluck('name')]);
                
                if ($roles->isNotEmpty()) {
                    // Set team ID explicitly for Spatie to sync roles with team_id
                    $tenantId = \Filament\Facades\Filament::getTenant()->id;
                    setPermissionsTeamId($tenantId);
                    
                    $record->syncRoles($roles);
                    \Illuminate\Support\Facades\Log::info('CreateUser: synced roles via Models for team ' . $tenantId);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('CreateUser: Failed to sync roles', ['error' => $e->getMessage()]);
        }

        return $record;
    }
}
