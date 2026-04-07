<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItemUpsell extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function upsellItem()
    {
        return $this->belongsTo(MenuItem::class, 'upsell_item_id');
    }
}
