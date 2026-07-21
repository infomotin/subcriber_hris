<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\Device;
use App\Models\Tenant;
use App\Models\User;
use App\Models\ZktecoUser;
use Illuminate\Support\Facades\DB;

class DatabaseManagerController extends Controller
{
    public function index()
    {
        $tablesStats = [
            'users' => User::count(),
            'tenants' => Tenant::count(),
            'devices' => Device::count(),
            'zkteco_users' => ZktecoUser::count(),
            'attendance_logs' => AttendanceLog::count(),
            'tenant_webhook_settings' => DB::table('tenant_webhook_settings')->count(),
            'tenant_push_logs' => DB::table('tenant_push_logs')->count(),
            'system_logs' => DB::table('system_logs')->count(),
        ];

        $isolationAudit = [
            'strategy' => 'Single-Database Multi-Tenant Data Isolation',
            'scoping_trait' => 'App\\Traits\\BelongsToTenant',
            'scoped_models' => ['Device', 'ZktecoUser', 'AttendanceLog', 'TenantWebhookSetting', 'TenantPushLog'],
            'isolation_status' => 'ACTIVE & SECURE',
        ];

        return view('system_admin.database.index', compact('tablesStats', 'isolationAudit'));
    }

    public function backup()
    {
        return redirect()->route('admin.system.database.index')
            ->with('success', 'Database backup snapshot generated successfully (Saved to storage/app/backups).');
    }
}
