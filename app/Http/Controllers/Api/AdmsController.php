<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdmsController extends Controller
{
    public function overview(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $devices = Device::where('tenant_id', $tenant->id)->get();

        return response()->json([
            'total_devices' => $devices->count(),
            'online_devices' => $devices->filter(fn($d) => $d->isOnline())->count(),
            'offline_devices' => $devices->filter(fn($d) => !$d->isOnline())->count(),
            'recent_activity' => $tenant->attendanceLogs()
                ->where('created_at', '>=', now()->subHour())
                ->count(),
            'listener_status' => Cache::get("listener_running_{$tenant->id}", false) ? 'running' : 'stopped',
        ]);
    }

    public function endpoint(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $endpoint = url('/api/device/push-attendance');
        $token = $request->user()->api_token;

        return response()->json([
            'endpoint' => $endpoint,
            'method' => 'POST',
            'auth' => 'Bearer ' . $token,
            'example_payload' => [
                'device_serial' => 'ACX12345',
                'records' => [['pin' => '1001', 'punch_at' => '2025-01-15 09:00:00', 'status' => 0]],
            ],
        ]);
    }

    public function punchLogs(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $logs = $tenant->attendanceLogs()
            ->with('device')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get()
            ->map(fn($l) => [
                'id' => $l->id,
                'device' => $l->device?->name,
                'pin' => $l->pin,
                'punched_at' => $l->punched_at->format('Y-m-d H:i:s'),
                'status' => $l->status == 0 ? 'IN' : 'OUT',
            ]);

        return response()->json($logs);
    }

    public function handshakeTest(Request $request): JsonResponse
    {
        $deviceIds = $request->input('device_ids', []);
        $results = [];

        foreach ($deviceIds as $id) {
            $device = Device::where('tenant_id', $request->user()->tenant_id)->find($id);
            if (!$device) {
                $results[] = ['device_id' => $id, 'status' => 'not_found'];
                continue;
            }
            $results[] = [
                'device_id' => $id,
                'name' => $device->name,
                'ip' => $device->ip_address,
                'status' => $device->isOnline() ? 'online' : 'offline',
                'last_heartbeat' => $device->last_heartbeat?->format('Y-m-d H:i:s'),
            ];
        }

        return response()->json($results);
    }

    public function listenerConfig(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $running = Cache::get("listener_running_{$tenant->id}", false);

        return response()->json([
            'listener_running' => $running,
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
            'last_listener_start' => Cache::get("listener_started_at_{$tenant->id}"),
            'cache_key' => "listener_running_{$tenant->id}",
        ]);
    }
}
