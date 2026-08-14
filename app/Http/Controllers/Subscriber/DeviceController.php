<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Tenant;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        if ($tenant) {
            app()->instance('current_tenant_id', $tenant->id);
            session(['tenant_id' => $tenant->id]);
        }

        $devices = Device::orderBy('id', 'desc')->paginate(15);
        return view('subscriber.devices.index', compact('devices', 'tenant'));
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();

        if (! $tenant) {
            return back()->with('error', 'Subscriber tenant context not found.');
        }

        app()->instance('current_tenant_id', $tenant->id);

        // Enforce Subscriber Machine Quota Limit
        if (! $tenant->canAddDevice()) {
            return back()->with('error', "Machine registration quota reached ({$tenant->devices()->count()} / {$tenant->max_devices}). Please upgrade your subscription plan to add more devices.");
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:devices,serial_number',
            'ip_address' => 'nullable|string|max:45',
        ]);

        Device::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['name'],
            'serial_number' => strtoupper(trim($validated['serial_number'])),
            'ip_address' => $validated['ip_address'] ?? $request->ip(),
            'status' => 'offline',
            'last_heartbeat' => null,
        ]);

        return redirect()->route('subscriber.devices.index')
            ->with('success', "Biometric machine '{$validated['name']}' registered successfully under your quota.");
    }

    public function checkStatus($id)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        $device = Device::where('tenant_id', $tenant->id)->findOrFail($id);

        return response()->json([
            'id' => $device->id,
            'name' => $device->name,
            'serial_number' => $device->serial_number,
            'ip_address' => $device->ip_address,
            'port' => $device->port,
            'firmware_version' => $device->firmware_version ?? 'Unknown',
            'push_version' => $device->push_version ?? 'Unknown',
            'status' => $device->isOnline() ? 'online' : 'offline',
            'online' => $device->isOnline(),
            'last_heartbeat' => $device->last_heartbeat?->format('Y-m-d H:i:s'),
            'last_heartbeat_humans' => $device->last_heartbeat ? $device->last_heartbeat->diffForHumans() : 'Never',
            'user_count' => $device->user_count,
            'att_count' => $device->att_count,
            'timezone' => $device->timezone ?? 'UTC',
            'realtime' => $device->realtime ?? false,
            'delay' => $device->delay ?? 30,
            'error_delay' => $device->error_delay ?? 60,
            'trans_times' => $device->trans_times ?? 'Not set',
            'trans_interval' => $device->trans_interval ?? 1,
            'trans_flag' => $device->trans_flag ?? 'Not set',
            'registered_at' => $device->created_at->format('Y-m-d H:i:s'),
        ]);
    }
}
