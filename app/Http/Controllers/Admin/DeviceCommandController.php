<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceCommand;
use Illuminate\Http\Request;

class DeviceCommandController extends Controller
{
    public function index(Request $request)
    {
        $query = DeviceCommand::with('device');

        if ($request->filled('device_id')) {
            $query->where('device_id', $request->device_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $commands = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $devices = Device::all();

        return view('admin.commands.index', compact('commands', 'devices'));
    }

    public function destroy(DeviceCommand $command)
    {
        $command->delete();
        return back()->with('success', 'Command deleted successfully.');
    }
}
