<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppFeature extends Model
{
    protected $fillable = [
        'tab',
        'title',
        'slug',
        'badge',
        'short_description',
        'long_description',
        'image_url',
        'bullets',
        'order_index',
        'is_active',
    ];

    protected $casts = [
        'bullets' => 'array',
        'is_active' => 'boolean',
    ];
}
