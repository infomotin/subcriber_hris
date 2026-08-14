<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\Device;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SysDashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $totalUsers = User::count();
        $totalDevices = Device::count();
        $onlineDevices = Device::all()->filter(fn($d) => $d->isOnline())->count();
        $totalAttendance = AttendanceLog::count();

        $health = [
            'database' => $this->checkDatabase(),
            'cache' => Cache::has('app_health_check'),
            'disk_free_gb' => round(disk_free_space('/') / 1024 / 1024 / 1024, 2),
            'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
        ];

        return response()->json([
            'total_tenants' => $totalTenants,
            'active_tenants' => $activeTenants,
            'suspended_tenants' => $totalTenants - $activeTenants,
            'total_users' => $totalUsers,
            'total_devices' => $totalDevices,
            'online_devices' => $onlineDevices,
            'offline_devices' => $totalDevices - $onlineDevices,
            'total_attendance_records' => $totalAttendance,
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
}
