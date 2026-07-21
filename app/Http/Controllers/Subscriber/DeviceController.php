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
}
