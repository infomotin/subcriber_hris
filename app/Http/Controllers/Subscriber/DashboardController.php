<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\Device;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\ZktecoUser;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $tenant = $user?->tenant ?? Tenant::first();

        if (! $tenant) {
            $tenant = Tenant::create([
                'name' => 'Default Organization',
                'tenant_token' => 'DEFAULTTOKEN12345',
                'status' => 'active',
                'max_devices' => 5,
                'expires_at' => now()->addMonth(),
            ]);
        }

        app()->instance('current_tenant_id', $tenant->id);
        session(['tenant_id' => $tenant->id]);

        $devicesCount = Device::count();
        $onlineDevicesCount = Device::get()->filter(fn ($d) => $d->isOnline())->count();
        $todayPunches = AttendanceLog::whereDate('punched_at', today())->count();
        $usersCount = ZktecoUser::count();

        $plans = SubscriptionPlan::where('status', 'active')->get();
        $currentPlan = $tenant->plan ?? SubscriptionPlan::first();

        $remainingDays = $tenant->expires_at
            ? max(0, (int) now()->diffInDays($tenant->expires_at, false))
            : 0;

        $recentLogs = AttendanceLog::with(['device', 'zktecoUser'])
            ->orderBy('punched_at', 'desc')
            ->take(10)
            ->get();

        return view('subscriber.dashboard', compact(
            'tenant',
            'devicesCount',
            'onlineDevicesCount',
            'todayPunches',
            'usersCount',
            'plans',
            'currentPlan',
            'remainingDays',
            'recentLogs'
        ));
    }
}
