<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $devices = Device::where('tenant_id', $tenant->id)
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 15);

        $devices->getCollection()->transform(function ($device) {
            return [
                'id' => $device->id,
                'serial_number' => $device->serial_number,
                'name' => $device->name,
                'ip_address' => $device->ip_address,
                'status' => $device->status,
                'is_online' => $device->isOnline(),
                'last_heartbeat' => $device->last_heartbeat?->format('Y-m-d H:i:s'),
                'att_count' => $device->att_count,
                'firmware_version' => $device->firmware_version,
                'created_at' => $device->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json($devices);
    }

    public function store(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (!$tenant->canAddDevice()) {
            return response()->json([
                'message' => "Device limit reached ({$tenant->devices()->count()}/{$tenant->max_devices})",
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:devices,serial_number',
            'ip_address' => 'nullable|string|max:45',
        ]);

        $device = Device::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['name'],
            'serial_number' => strtoupper(trim($validated['serial_number'])),
            'ip_address' => $validated['ip_address'] ?? $request->ip(),
            'status' => 'offline',
            'last_heartbeat' => null,
        ]);

        return response()->json([
            'message' => 'Device registered',
            'device' => $device,
        ], 201);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $device = Device::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);
        return response()->json(['device' => $device]);
    }
}
