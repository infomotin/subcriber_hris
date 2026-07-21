<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NetworkSetting;
use Illuminate\Http\Request;

class NetworkSettingController extends Controller
{
    public function index()
    {
        $settings = [
            'server_ip' => NetworkSetting::get('server_ip', config('zkteco-adms.server_ip', '0.0.0.0')),
            'server_port' => NetworkSetting::get('server_port', config('zkteco-adms.server_port', 8000)),
            'heartbeat_timeout' => NetworkSetting::get('heartbeat_timeout', config('zkteco-adms.heartbeat_timeout', 120)),
            'response_delay' => NetworkSetting::get('response_delay', config('zkteco-adms.response.delay', 30)),
            'error_delay' => NetworkSetting::get('error_delay', config('zkteco-adms.response.error_delay', 60)),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'server_ip' => 'required|string',
            'server_port' => 'required|integer|min:1|max:65535',
            'heartbeat_timeout' => 'required|integer|min:10|max:3600',
            'response_delay' => 'required|integer|min:1|max:600',
            'error_delay' => 'required|integer|min:1|max:600',
        ]);

        foreach ($validated as $key => $value) {
            NetworkSetting::set($key, $value);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Network and server settings updated successfully.');
    }

    public function testConnection(Request $request)
    {
        $request->validate([
            'host' => 'required|string',
            'port' => 'required|integer|min:1|max:65535',
        ]);

        $host = $request->host;
        $port = (int) $request->port;

        $connectionResult = @fsockopen($host, $port, $errno, $errstr, 5);

        if (is_resource($connectionResult)) {
            fclose($connectionResult);
            return response()->json([
                'success' => true,
                'message' => "Successfully connected to {$host}:{$port}! Port is open and listening.",
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => "Could not connect to {$host}:{$port}. Error ({$errno}): {$errstr}. Please check server firewall/router port forwarding.",
        ], 400);
    }
}
