<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceCommand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDeviceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $devices = Device::with('tenant')
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 15);

        $devices->getCollection()->transform(function ($device) {
            return [
                'id' => $device->id,
                'tenant_id' => $device->tenant_id,
                'tenant_name' => $device->tenant?->name,
                'serial_number' => $device->serial_number,
                'name' => $device->name,
                'ip_address' => $device->ip_address,
                'status' => $device->status,
                'is_online' => $device->isOnline(),
                'last_heartbeat' => $device->last_heartbeat?->format('Y-m-d H:i:s'),
                'firmware_version' => $device->firmware_version,
                'created_at' => $device->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json($devices);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:devices,serial_number',
            'ip_address' => 'nullable|string|max:45',
        ]);

        $device = Device::create([
            'tenant_id' => $validated['tenant_id'],
            'name' => $validated['name'],
            'serial_number' => strtoupper(trim($validated['serial_number'])),
            'ip_address' => $validated['ip_address'] ?? $request->ip(),
            'status' => 'offline',
        ]);

        return response()->json(['message' => 'Device created', 'device' => $device], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $device = Device::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'serial_number' => 'sometimes|string|max:255|unique:devices,serial_number,' . $id,
            'ip_address' => 'nullable|string|max:45',
            'tenant_id' => 'sometimes|exists:tenants,id',
        ]);

        $device->update($validated);
        return response()->json(['message' => 'Device updated', 'device' => $device]);
    }

    public function destroy($id): JsonResponse
    {
        $device = Device::findOrFail($id);
        $device->delete();
        return response()->json(['message' => 'Device deleted']);
    }

    public function reboot($id): JsonResponse
    {
        $device = Device::findOrFail($id);
        DeviceCommand::create([
            'tenant_id' => $device->tenant_id,
            'device_id' => $device->id,
            'command' => 'reboot',
            'type' => 'system',
            'status' => 'pending',
        ]);
        return response()->json(['message' => 'Reboot command queued for device', 'device_id' => (int) $id]);
    }

    public function clearLogs($id): JsonResponse
    {
        $device = Device::findOrFail($id);
        DeviceCommand::create([
            'tenant_id' => $device->tenant_id,
            'device_id' => $device->id,
            'command' => 'clear_logs',
            'type' => 'system',
            'status' => 'pending',
        ]);
        return response()->json(['message' => 'Clear logs command queued', 'device_id' => (int) $id]);
    }

    public function queryInfo($id): JsonResponse
    {
        $device = Device::findOrFail($id);
        DeviceCommand::create([
            'tenant_id' => $device->tenant_id,
            'device_id' => $device->id,
            'command' => 'query_info',
            'type' => 'info',
            'status' => 'pending',
        ]);
        return response()->json(['message' => 'Info query queued', 'device_id' => (int) $id, 'device' => $device]);
    }
}
