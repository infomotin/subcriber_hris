<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\Device;
use App\Models\ZktecoUser;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $devices = Device::all();
        $onlineDevices = $devices->filter(fn ($d) => $d->isOnline())->count();
        $totalDevices = $devices->count();

        $todayPunches = AttendanceLog::whereDate('punched_at', today())->count();
        $totalUsers = ZktecoUser::count();

        $recentLogs = AttendanceLog::with(['device', 'zktecoUser'])
            ->orderBy('punched_at', 'desc')
            ->take(10)
            ->get();

        // Attendance stats for last 7 days
        $last7Days = collect(range(6, 0))->mapWithKeys(function ($daysAgo) {
            $date = now()->subDays($daysAgo)->format('Y-m-d');
            $count = AttendanceLog::whereDate('punched_at', $date)->count();
            return [now()->subDays($daysAgo)->format('M d') => $count];
        });

        return view('admin.dashboard', compact(
            'totalDevices',
            'onlineDevices',
            'todayPunches',
            'totalUsers',
            'recentLogs',
            'last7Days',
            'devices'
        ));
    }
}
