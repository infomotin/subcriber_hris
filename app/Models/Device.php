<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use HasFactory, BelongsToTenant, SoftDeletes;

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

    public function markAsOnline(?string $sourceIp = null): void
    {
        $data = [
            'last_heartbeat' => now(),
            'status' => 'online',
        ];

        if ($sourceIp) {
            $data['ip_address'] = $sourceIp;
        }

        $this->update($data);
    }

    /**
     * True when the most recent heartbeat came from an actual device
     * (i.e. not from the server itself / localhost / Cloudflare-only tests).
     */
    public function hasRealDeviceTraffic(): bool
    {
        $ip = $this->ip_address ?? '';

        if ($ip === '' || $ip === '127.0.0.1' || $ip === '::1') {
            return false;
        }

        $serverIp = config('zkteco-adms.server_ip', '');
        if ($serverIp !== '' && $ip === $serverIp) {
            return false;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }

        return true;
    }
}
