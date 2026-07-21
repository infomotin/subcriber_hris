<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\ZktecoUser;
use App\Services\DeviceCommandBuilder;
use Illuminate\Http\Request;

class ZktecoUserController extends Controller
{
    public function __construct(
        protected DeviceCommandBuilder $commandBuilder
    ) {}

    public function index(Request $request)
    {
        $query = ZktecoUser::with('device');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pin', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('card_number', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('pin', 'asc')->paginate(20)->withQueryString();
        $devices = Device::all();

        return view('admin.users.index', compact('users', 'devices'));
    }

    public function create()
    {
        $devices = Device::all();
        return view('admin.users.create', compact('devices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pin' => 'required|string|unique:zkteco_users,pin',
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|max:50',
            'card_number' => 'nullable|string|max:50',
            'privilege' => 'required|integer',
            'device_id' => 'nullable|exists:devices,id',
        ]);

        $user = ZktecoUser::create($validated);

        if ($request->filled('push_to_device') && $request->filled('device_id')) {
            $device = Device::find($request->device_id);
            if ($device) {
                $this->commandBuilder->syncUser($device, $user);
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(ZktecoUser $user)
    {
        $devices = Device::all();
        return view('admin.users.edit', compact('user', 'devices'));
    }

    public function update(Request $request, ZktecoUser $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|max:50',
            'card_number' => 'nullable|string|max:50',
            'privilege' => 'required|integer',
            'device_id' => 'nullable|exists:devices,id',
        ]);

        $user->update($validated);

        if ($request->filled('push_to_device') && $user->device_id) {
            $device = Device::find($user->device_id);
            if ($device) {
                $this->commandBuilder->syncUser($device, $user);
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(ZktecoUser $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function pushToDevice(ZktecoUser $user, Device $device)
    {
        $this->commandBuilder->syncUser($device, $user);
        return back()->with('success', "Sync command queued to push User {$user->pin} to Device {$device->serial_number}");
    }
}
