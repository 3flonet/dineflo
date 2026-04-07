<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderFeedback extends Model
{
    protected $table = 'order_feedbacks';

    protected $fillable = [
        'restaurant_id',
        'order_id',
        'rating',
        'comment',
        'categories',
        'is_public',
        'replied_at',
        'replied_by',
        'reply_comment',
    ];

    protected $casts = [
        'categories' => 'array',
        'is_public' => 'boolean',
        'replied_at' => 'datetime',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function replier()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }
}
