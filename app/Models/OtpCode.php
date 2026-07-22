<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpCode extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'type',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeValid($query, string $type = 'two_factor')
    {
        return $query->where('type', $type)
            ->whereNull('used_at')
            ->where('expires_at', '>', now());
    }

    public function markAsUsed(): void
    {
        $this->update(['used_at' => now()]);
    }
}
