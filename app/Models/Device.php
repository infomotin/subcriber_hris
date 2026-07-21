<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'serial_number',
        'name',
        'ip_address',
        'port',
        'firmware_version',
        'push_version',
        'user_count',
        'fp_count',
        'face_count',
        'att_count',
        'last_heartbeat',
        'status',
        'timezone',
        'realtime',
        'delay',
        'error_delay',
        'trans_times',
        'trans_interval',
        'trans_flag',
        'time_sync',
    ];

    protected $casts = [
        'last_heartbeat' => 'datetime',
        'realtime' => 'boolean',
        'time_sync' => 'boolean',
        'user_count' => 'integer',
        'fp_count' => 'integer',
        'face_count' => 'integer',
        'att_count' => 'integer',
        'delay' => 'integer',
        'error_delay' => 'integer',
        'trans_interval' => 'integer',
    ];

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function commands(): HasMany
    {
        return $this->hasMany(DeviceCommand::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(ZktecoUser::class);
    }

    public function isOnline(): bool
    {
        if (! $this->last_heartbeat) {
            return false;
        }

        $timeout = config('zkteco-adms.heartbeat_timeout', 120);
        return $this->last_heartbeat->diffInSeconds(now()) <= $timeout;
    }

    public function markAsOnline(): void
    {
        $this->update([
            'last_heartbeat' => now(),
            'status' => 'online',
        ]);
    }
}
