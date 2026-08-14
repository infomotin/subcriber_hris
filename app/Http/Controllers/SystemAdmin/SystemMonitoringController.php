<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use App\Models\TenantPushLog;
use App\Services\SystemMonitorService;
use Illuminate\Http\Request;

class SystemMonitoringController extends Controller
{
    public function __construct(
        protected SystemMonitorService $monitorService
    ) {}

    public function index(Request $request)
    {
        $category = $request->query('category', 'all');

        $query = SystemLog::orderBy('id', 'desc');

        if ($category !== 'all') {
            $query->where('level', $category);
        }

        $logs = $query->paginate(20);

        $metrics = $this->monitorService->getSystemMetrics();
        $healthCheck = $this->monitorService->runHealthCheck();

        $pushLogs = TenantPushLog::orderBy('id', 'desc')->take(15)->get();

        $listeningPorts = $this->monitorService->getListeningPorts();
        $zkFlow = $this->monitorService->getZKDeviceFlow();
        $portActivity = $this->monitorService->getPortActivity();

        return view('system_admin.monitoring.index', compact(
            'logs', 'metrics', 'healthCheck', 'pushLogs', 'category',
            'listeningPorts', 'zkFlow', 'portActivity'
        ));
    }
}
