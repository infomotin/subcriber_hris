<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SysMonitoringController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $logs = SystemLog::orderBy('id', 'desc')
            ->limit(100)
            ->get();

        $metrics = [
            'total_tenants' => DB::table('tenants')->count(),
            'active_tenants' => DB::table('tenants')->where('status', 'active')->count(),
            'total_users' => DB::table('users')->count(),
            'total_devices' => DB::table('devices')->count(),
            'total_employees' => DB::table('employee_profiles')->count(),
            'recent_errors' => SystemLog::where('level', 'error')
                ->where('created_at', '>=', now()->subDay())
                ->count(),
        ];

        $health = [
            'database' => $this->checkDatabase(),
            'cache' => Cache::has('app_health_check'),
            'disk_free_gb' => round(disk_free_space('/') / 1024 / 1024 / 1024, 2),
            'memory_usage_mb' => memory_get_usage(true) / 1024 / 1024,
            'uptime' => $this->getUptime(),
        ];

        return response()->json([
            'logs' => $logs,
            'metrics' => $metrics,
            'health' => $health,
        ]);
    }

    protected function checkDatabase(): bool
    {
        try {
            DB::select('SELECT 1');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function getUptime(): ?string
    {
        if (file_exists('/proc/uptime')) {
            $uptime = (float) file_get_contents('/proc/uptime');
            $days = floor($uptime / 86400);
            $hours = floor(($uptime % 86400) / 3600);
            return "{$days}d {$hours}h";
        }
        return null;
    }
}
