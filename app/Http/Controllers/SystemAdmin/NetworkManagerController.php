<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\NetworkSetting;
use Illuminate\Http\Request;

class NetworkManagerController extends Controller
{
    public function index()
    {
        $setting = (object) [
            'adms_port' => (int) NetworkSetting::get('adms_port', 80),
            'gateway_ip' => NetworkSetting::get('gateway_ip', env('ADMS_SERVER_IP', '15.235.229.40')),
            'push_interval' => (int) NetworkSetting::get('push_interval', 30),
            'is_adms_active' => (bool) NetworkSetting::get('is_adms_active', true),
        ];

        $devices = Device::withoutGlobalScopes()->orderBy('last_heartbeat', 'desc')->get();

        $activeDevices = $devices->map(function ($dev) {
            return (object) [
                'id' => $dev->id,
                'serial_number' => $dev->serial_number,
                'ip_address' => $dev->ip_address,
                'name' => $dev->name,
                'tenant' => $dev->tenant,
                'last_heartbeat' => $dev->last_heartbeat,
                'is_online' => $dev->isOnline(),
                'status' => $dev->isOnline() ? 'online' : 'offline',
            ];
        });

        return view('system_admin.network.index', compact('setting', 'activeDevices'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'adms_port' => 'required|integer|min:80|max:65535',
            'gateway_ip' => 'required|string|max:255',
            'push_interval' => 'required|integer|min:5|max:3600',
            'is_adms_active' => 'nullable|boolean',
        ]);

        NetworkSetting::set('adms_port', $validated['adms_port'], 'ADMS Server Listener Port');
        NetworkSetting::set('gateway_ip', $validated['gateway_ip'], 'Gateway IP / Domain');
        NetworkSetting::set('push_interval', $validated['push_interval'], 'Heartbeat Interval');
        NetworkSetting::set('is_adms_active', $request->has('is_adms_active') ? '1' : '0', 'ADMS Gateway Active');

        return redirect()->route('admin.system.network.index')
            ->with('success', 'ZKTeco ADMS Network Server settings updated successfully.');
    }
}
