<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\PaymentLog;
use App\Models\SystemLog;
use App\Models\Tenant;
use App\Services\SystemMonitorService;

class DashboardController extends Controller
{
    public function __construct(
        protected SystemMonitorService $monitorService
    ) {}

    public function index()
    {
        $metrics = $this->monitorService->getSystemMetrics();

        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $totalRevenue = PaymentLog::where('status', 'success')->sum('amount');
        $recentSystemLogs = SystemLog::orderBy('id', 'desc')->take(10)->get();

        return view('system_admin.dashboard', compact(
            'metrics',
            'totalTenants',
            'activeTenants',
            'totalRevenue',
            'recentSystemLogs'
        ));
    }
}
