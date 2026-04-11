<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function getFeaturedImage(): ?string
    {
        if ($this->thumbnail) {
            return \Illuminate\Support\Facades\Storage::url($this->thumbnail);
        }
        
        return $this->thumbnail_url;
    }
}
