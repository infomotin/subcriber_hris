<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        if (!$tenant) {
            return response()->json(['message' => 'No tenant found'], 404);
        }

        $totalEmployees = $tenant->employees()->count();
        $totalDevices = $tenant->devices()->count();
        $onlineDevices = $tenant->devices()->get()->filter(fn($d) => $d->isOnline())->count();
        $recentPunches = \DB::table('raw_punch_data')
            ->where('tenant_id', $tenant->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        return response()->json([
            'total_employees' => $totalEmployees,
            'employee_limit' => $tenant->max_employees,
            'total_devices' => $totalDevices,
            'device_limit' => $tenant->max_devices,
            'online_devices' => $onlineDevices,
            'offline_devices' => $totalDevices - $onlineDevices,
            'recent_punches_24h' => $recentPunches,
            'tenant' => [
                'name' => $tenant->name,
                'status' => $tenant->status,
                'expires_at' => $tenant->expires_at?->format('Y-m-d'),
            ],
        ]);
    }
}
