<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NetworkSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = NetworkSetting::all()->pluck('value', 'key');
        return response()->json($settings);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => 'required|string',
            'value' => 'required|string',
            'description' => 'nullable|string',
        ]);

        NetworkSetting::set($validated['key'], $validated['value'], $validated['description'] ?? null);
        return response()->json(['message' => 'Setting updated', 'key' => $validated['key'], 'value' => $validated['value']]);
    }

    public function testConnection(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => 'required|integer|exists:devices,id',
        ]);

        $device = \App\Models\Device::find($validated['device_id']);
        if (!$device) {
            return response()->json(['message' => 'Device not found', 'reachable' => false], 404);
        }

        $reachable = false;
        if ($device->ip_address) {
            $output = [];
            $exitCode = 0;
            exec('ping -c 1 -W 2 ' . escapeshellarg($device->ip_address) . ' 2>&1', $output, $exitCode);
            $reachable = $exitCode === 0;
        }

        return response()->json([
            'device_id' => $device->id,
            'device_name' => $device->name,
            'ip_address' => $device->ip_address,
            'reachable' => $reachable,
            'is_online' => $device->isOnline(),
            'last_heartbeat' => $device->last_heartbeat?->format('Y-m-d H:i:s'),
        ]);
    }
}
