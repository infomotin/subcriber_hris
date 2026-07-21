<?php

namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\Device;
use App\Models\Tenant;
use App\Models\User;
use App\Models\ZktecoUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemoController extends Controller
{
    public function index()
    {
        $demoTenant = Tenant::where('is_demo', true)->first();

        if (! $demoTenant) {
            $demoTenant = Tenant::create([
                'name' => 'Public Demo Sandbox',
                'tenant_token' => 'DEMOSANDBOXTOKEN123',
                'status' => 'active',
                'max_devices' => 2,
                'is_demo' => true,
            ]);
        }

        app()->instance('current_tenant_id', $demoTenant->id);
        session(['tenant_id' => $demoTenant->id, 'is_demo_session' => true]);

        $devicesCount = Device::count();
        $logsCount = AttendanceLog::count();
        $usersCount = ZktecoUser::count();

        return view('demo.dashboard', compact('demoTenant', 'devicesCount', 'logsCount', 'usersCount'));
    }

    public function destroyDemoSession(Request $request)
    {
        $tenantId = session('tenant_id');
        $demoTenant = Tenant::where('id', $tenantId)->where('is_demo', true)->first();

        if ($demoTenant) {
            // Destroy all demo sandbox data
            Device::withoutGlobalScopes()->where('tenant_id', $demoTenant->id)->delete();
            AttendanceLog::withoutGlobalScopes()->where('tenant_id', $demoTenant->id)->delete();
            ZktecoUser::withoutGlobalScopes()->where('tenant_id', $demoTenant->id)->delete();
        }

        session()->forget(['tenant_id', 'is_demo_session']);

        return redirect()->route('demo.dashboard')
            ->with('success', 'Public Demo Sandbox session and temporary data destroyed.');
    }
}
