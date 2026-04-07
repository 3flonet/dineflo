<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    public static function bootBelongsToTenant()
    {
        // 1. Global Scope for automatic filtering
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();
                if ($user->hasRole('super_admin')) return;

                if (method_exists($user, 'getAccessibleRestaurantIds')) {
                    $ids = $user->getAccessibleRestaurantIds();
                    if ($ids !== null) {
                        $column = property_exists(static::class, 'tenantColumn') ? static::$tenantColumn : 'restaurant_id';
                        $builder->whereIn($table = $builder->getModel()->getTable() . '.' . $column, $ids);
                    }
                }
            }
        });

        // 2. Automatically set tenant ID when creating
        static::creating(function ($model) {
            $column = property_exists(static::class, 'tenantColumn') ? static::$tenantColumn : 'restaurant_id';
            
            if (!$model->{$column}) {
                // Try to get from Filament tenant context first
                if (class_exists('\Filament\Facades\Filament') && \Filament\Facades\Filament::getTenant()) {
                    $model->{$column} = \Filament\Facades\Filament::getTenant()->id;
                } 
                // Fallback to current authenticated user's restaurant_id
                elseif (auth()->check() && auth()->user()->restaurant_id) {
                    $model->{$column} = auth()->user()->restaurant_id;
                }
            }
        });
    }
}
