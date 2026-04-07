<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('orders.{restaurantId}', function ($user, $restaurantId) {
    if ($user->hasRole('super_admin')) return true;
    
    // Check access
    $ids = $user->getAccessibleRestaurantIds();
    return $ids && $ids->contains($restaurantId);
});

// Restaurant private channel for waiter calls and real-time updates
Broadcast::channel('restaurant.{restaurantId}', function ($user, $restaurantId) {
    if ($user->hasRole('super_admin')) return true;
    
    // Check if user has access to this restaurant
    $ids = $user->getAccessibleRestaurantIds();
    return $ids && $ids->contains($restaurantId);
});
