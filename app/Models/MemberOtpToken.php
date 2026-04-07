<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberOtpToken extends Model
{
    protected $fillable = ['member_id', 'token', 'token_hash', 'expires_at', 'is_used', 'attempts'];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used'    => 'boolean',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }
}
