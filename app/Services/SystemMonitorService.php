<?php

namespace App\Services;

use App\Models\Device;
use App\Models\SystemLog;
use Illuminate\Support\Facades\DB;

class SystemMonitorService
{
    public function getSystemMetrics(): array
    {
        $phpVersion = PHP_VERSION;
        $laravelVersion = app()->version();

        // Database status & ping
        $dbStatus = 'Healthy';
        $dbResponseTimeMs = 0;
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $dbResponseTimeMs = round((microtime(true) - $start) * 1000, 2);
        } catch (\Throwable $e) {
            $dbStatus = 'Error: ' . $e->getMessage();
        }

        // Memory Usage
        $memoryUsageMb = round(memory_get_usage(true) / 1024 / 1024, 2);
        $peakMemoryMb = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

        // Network Devices Pulse
        $totalDevices = Device::withoutGlobalScopes()->count();
        $onlineDevices = Device::withoutGlobalScopes()->get()->filter(fn ($d) => $d->isOnline())->count();
        $offlineDevices = $totalDevices - $onlineDevices;

        return [
            'php_version' => $phpVersion,
            'laravel_version' => $laravelVersion,
            'db_status' => $dbStatus,
            'db_response_time_ms' => $dbResponseTimeMs,
            'memory_usage_mb' => $memoryUsageMb,
            'peak_memory_mb' => $peakMemoryMb,
            'total_devices' => $totalDevices,
            'online_devices' => $onlineDevices,
            'offline_devices' => $offlineDevices,
        ];
    }
}
