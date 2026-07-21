<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'pin',
        'punched_at',
        'status',
        'verify_type',
        'work_code',
        'reserved_1',
        'reserved_2',
        'raw_data',
    ];

    protected $casts = [
        'punched_at' => 'datetime',
        'status' => 'integer',
        'verify_type' => 'integer',
        'work_code' => 'integer',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function zktecoUser(): BelongsTo
    {
        return $this->belongsTo(ZktecoUser::class, 'pin', 'pin');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            0 => 'Check In',
            1 => 'Check Out',
            2 => 'Break Out',
            3 => 'Break In',
            4 => 'Overtime In',
            5 => 'Overtime Out',
            default => 'Unknown (' . $this->status . ')',
        };
    }

    public function getVerifyTypeLabelAttribute(): string
    {
        return match ($this->verify_type) {
            0 => 'Password',
            1 => 'Fingerprint',
            2 => 'Card',
            15 => 'Face',
            default => 'Other (' . $this->verify_type . ')',
        };
    }
}
