<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ZktecoUser extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'device_id',
        'pin',
        'name',
        'password',
        'card_number',
        'privilege',
        'is_synced',
    ];

    protected $casts = [
        'privilege' => 'integer',
        'is_synced' => 'boolean',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class, 'pin', 'pin');
    }

    public function getPrivilegeLabelAttribute(): string
    {
        return match ($this->privilege) {
            0 => 'Normal User',
            14 => 'Administrator',
            default => 'Custom (' . $this->privilege . ')',
        };
    }
}
