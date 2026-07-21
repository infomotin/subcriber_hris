<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Services\DeviceCommandBuilder;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function __construct(
        protected DeviceCommandBuilder $commandBuilder
    ) {}

    public function index()
    {
        $devices = Device::withCount(['attendanceLogs', 'users', 'commands'])->paginate(15);
        return view('admin.devices.index', compact('devices'));
    }

    public function create()
    {
        return view('admin.devices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'serial_number' => 'required|string|unique:devices,serial_number',
            'name' => 'nullable|string|max:255',
            'ip_address' => 'nullable|ip',
            'port' => 'nullable|integer',
            'delay' => 'nullable|integer',
            'error_delay' => 'nullable|integer',
        ]);

        $device = Device::create($validated);

        return redirect()->route('admin.devices.show', $device)
            ->with('success', 'Device created successfully.');
    }

    public function show(Device $device)
    {
        $device->load(['commands' => fn ($q) => $q->orderBy('id', 'desc')->take(10)]);
        $recentLogs = $device->attendanceLogs()->orderBy('punched_at', 'desc')->take(10)->get();

        return view('admin.devices.show', compact('device', 'recentLogs'));
    }

    public function edit(Device $device)
    {
        return view('admin.devices.edit', compact('device'));
    }

    public function update(Request $request, Device $device)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'nullable|ip',
            'port' => 'nullable|integer',
            'delay' => 'nullable|integer',
            'error_delay' => 'nullable|integer',
            'realtime' => 'boolean',
        ]);

        $device->update($validated);

        return redirect()->route('admin.devices.show', $device)
            ->with('success', 'Device updated successfully.');
    }

    public function destroy(Device $device)
    {
        $device->delete();
        return redirect()->route('admin.devices.index')
            ->with('success', 'Device deleted successfully.');
    }

    public function reboot(Device $device)
    {
        $this->commandBuilder->reboot($device);
        return back()->with('success', 'Reboot command queued for device ' . $device->serial_number);
    }

    public function clearLogs(Device $device)
    {
        $this->commandBuilder->clearAttendanceLogs($device);
        return back()->with('success', 'Clear logs command queued for device ' . $device->serial_number);
    }

    public function queryInfo(Device $device)
    {
        $this->commandBuilder->info($device);
        return back()->with('success', 'Query Info command queued for device ' . $device->serial_number);
    }
}
