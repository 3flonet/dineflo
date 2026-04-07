<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait TenancyScope
{
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->check()) {
            $user = auth()->user();
            
            // Method from User model
            if (method_exists($user, 'getAccessibleRestaurantIds')) {
                $restaurantIds = $user->getAccessibleRestaurantIds();

                // If null, user is super admin (full access)
                if ($restaurantIds !== null) {
                    $column = static::getTenancyColumn();
                    $query->whereIn($column, $restaurantIds);
                }
            }
        }

        return $query;
    }

    public static function getTenancyColumn(): string
    {
        return 'restaurant_id';
    }
}
