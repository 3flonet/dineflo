<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeddingPackage extends Model
{
    protected $guarded = [];

    protected $casts = [
        'gallery' => 'array',
        'inclusions' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
