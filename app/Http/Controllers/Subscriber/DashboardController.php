<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\Advance;
use App\Models\AttendanceLog;
use App\Models\Bill;
use App\Models\Department;
use App\Models\Device;
use App\Models\EmployeeProfile;
use App\Models\LeaveApplication;
use App\Models\MovementPass;
use App\Models\SalaryStructure;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\ZktecoUser;
use App\Models\TenantConfig;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return redirect()->route('subscriber.hr-dashboard');
    }

    public function admsOverview()
    {
        $data = $this->prepareDashboardData();
        return view('subscriber.adms.overview', $data);
    }

    public function admsEndpoint()
    {
        $data = $this->prepareDashboardData();
        $data['serverIp'] = env('ADMS_SERVER_IP', '15.235.229.40');
        $data['serverHost'] = request()->getHttpHost();
        return view('subscriber.adms.endpoint', $data);
    }

    public function admsPunchLogs(Request $request)
    {
        $data = $this->prepareDashboardData();
        $devices = Device::withoutGlobalScopes()->orderBy('name')->get();
        $selectedDevice = $request->input('device_id');

        $query = AttendanceLog::with(['device', 'zktecoUser'])
            ->orderBy('punched_at', 'desc');

        if ($selectedDevice) {
            $query->where('device_id', $selectedDevice);
        }

        $recentLogs = $query->take(50)->get();

        return view('subscriber.adms.punch-logs', array_merge($data, [
            'devices' => $devices,
            'selectedDevice' => $selectedDevice,
            'recentLogs' => $recentLogs,
        ]));
    }

    public function admsHandshakeTest()
    {
        $data = $this->prepareDashboardData();
        $data['serverIp'] = env('ADMS_SERVER_IP', '15.235.229.40');
        $data['serverHost'] = request()->getHttpHost();
        $data['devices'] = Device::withoutGlobalScopes()->orderBy('name')->get();
        return view('subscriber.adms.handshake-test', $data);
    }

    public function sendDeviceCommand(Request $request)
    {
        $request->validate([
            'device_id' => 'required|exists:devices,id',
            'command' => 'required|in:info,reboot,clear_log,get_users,delete_user',
            'pin' => 'nullable|string|max:20',
        ]);

        $device = Device::withoutGlobalScopes()->findOrFail($request->device_id);
        $cmdBuilder = app(\App\Services\DeviceCommandBuilder::class);

        switch ($request->command) {
            case 'info':
                $cmd = $cmdBuilder->info($device);
                break;
            case 'reboot':
                $cmd = $cmdBuilder->reboot($device);
                break;
            case 'clear_log':
                $cmd = $cmdBuilder->clearAttendanceLogs($device);
                break;
            case 'get_users':
                $cmd = $device->commands()->create([
                    'command' => 'DATA QUERY USERINFO',
                    'type' => 'GET_USERS',
                    'status' => 'pending',
                ]);
                break;
            case 'delete_user':
                if (! $request->pin) {
                    return response()->json(['error' => 'PIN is required to delete a user.'], 422);
                }
                $cmd = $cmdBuilder->deleteUser($device, $request->pin);
                break;
        }

        return response()->json([
            'success' => true,
            'command_id' => $cmd->id,
            'command' => $cmd->command,
            'formatted' => $cmd->formatted_command,
            'type' => $cmd->type,
            'status' => $cmd->status,
            'device' => $device->name . ' (' . $device->serial_number . ')',
            'device_online' => $device->isOnline(),
            'message' => $device->isOnline()
                ? 'Command queued. Device is ONLINE and will receive it shortly.'
                : 'Command queued. Device is OFFLINE — command will be delivered when device reconnects.',
        ]);
    }

    public function checkCommandStatus(Request $request)
    {
        $request->validate(['command_id' => 'required|exists:device_commands,id']);

        $cmd = \App\Models\DeviceCommand::withoutGlobalScopes()->with('device')->findOrFail($request->command_id);

        return response()->json([
            'id' => $cmd->id,
            'command' => $cmd->command,
            'type' => $cmd->type,
            'status' => $cmd->status,
            'return_code' => $cmd->return_code,
            'response' => $cmd->response,
            'executed_at' => $cmd->executed_at ? $cmd->executed_at->toDateTimeString() : null,
            'device' => $cmd->device->name ?? 'Unknown',
            'device_online' => $cmd->device ? $cmd->device->isOnline() : false,
        ]);
    }

    public function deviceStatus(Request $request)
    {
        $request->validate(['device_id' => 'required|exists:devices,id']);
        $device = Device::withoutGlobalScopes()->findOrFail($request->device_id);

        return response()->json([
            'id' => $device->id,
            'name' => $device->name,
            'serial_number' => $device->serial_number,
            'is_online' => $device->isOnline(),
            'last_heartbeat' => $device->last_heartbeat ? $device->last_heartbeat->diffForHumans() : 'Never',
            'ip_address' => $device->ip_address,
        ]);
    }

    /**
     * Show ADMS Listener & Server Configuration page
     */
    public function admsListenerConfig()
    {
        $data = $this->prepareDashboardData();
        $tenant = $data['tenant'];

        // Load existing configs from TenantConfig (group: 'adms_listener')
        $config = TenantConfig::getGroup('adms_listener');

        $data['listener_port'] = $config['listener_port'] ?? '80';
        $data['server_gateway'] = $config['server_gateway'] ?? request()->getHttpHost();
        $data['heartbeat_interval'] = $config['heartbeat_interval'] ?? '30';
        $data['gateway_enabled'] = $config['gateway_enabled'] ?? '1';

        return view('subscriber.adms.listener-config', $data);
    }

    /**
     * Save ADMS Listener & Server Configuration
     */
    public function updateAdmsListenerConfig(Request $request)
    {
        $validated = $request->validate([
            'listener_port' => 'required|integer|min:1|max:65535',
            'server_gateway' => 'required|string|max:255',
            'heartbeat_interval' => 'required|integer|min:5|max:3600',
            'gateway_enabled' => 'required|in:0,1',
        ]);

        TenantConfig::setGroup('adms_listener', [
            'listener_port' => $validated['listener_port'],
            'server_gateway' => $validated['server_gateway'],
            'heartbeat_interval' => $validated['heartbeat_interval'],
            'gateway_enabled' => $validated['gateway_enabled'],
        ]);

        return redirect()->route('subscriber.adms.listener-config')
            ->with('success', 'ADMS Listener & Server Configuration saved successfully.');
    }

    public function subscriptionOverview()
    {
        $data = $this->prepareDashboardData();
        return view('subscriber.subscription.overview', $data);
    }

    private function prepareDashboardData()
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

        return compact(
            'tenant',
            'devicesCount',
            'onlineDevicesCount',
            'todayPunches',
            'usersCount',
            'plans',
            'currentPlan',
            'remainingDays',
            'recentLogs'
        );
    }

    public function stats(Request $request)
    {
        $user = auth()->user();
        $tenant = $user?->tenant ?? Tenant::first();
        if (! $tenant) {
            return response()->json(['error' => 'No tenant'], 404);
        }

        app()->instance('current_tenant_id', $tenant->id);

        $selectedDevice = $request->input('device_id');

        $todayQuery = AttendanceLog::whereDate('punched_at', today());
        if ($selectedDevice) {
            $todayQuery->where('device_id', $selectedDevice);
        }
        $todayPunches = $todayQuery->count();

        $logsQuery = AttendanceLog::with(['device', 'zktecoUser'])
            ->orderBy('punched_at', 'desc');

        if ($selectedDevice) {
            $logsQuery->where('device_id', $selectedDevice);
        }

        $recentLogs = $logsQuery->take(10)
            ->get()
            ->map(fn ($log) => [
                'id' => $log->id,
                'pin' => $log->pin,
                'user_name' => $log->zktecoUser->name ?? 'User #' . $log->pin,
                'device_serial' => $log->device->serial_number ?? 'N/A',
                'punched_at' => $log->punched_at->format('M d, Y h:i:s A'),
                'status_label' => $log->status_label,
                'verify_type_label' => $log->verify_type_label,
            ]);

        return response()->json([
            'today_punches' => $todayPunches,
            'recent_logs' => $recentLogs,
        ]);
    }

    public function hrDashboard()
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

        $totalEmployees = EmployeeProfile::count();
        $activeEmployees = EmployeeProfile::where('status', 'active')->count();
        $departments = Department::withCount('employees')->get();
        $todayLogs = AttendanceLog::whereDate('punched_at', today())->count();
        $todayCheckIns = AttendanceLog::whereDate('punched_at', today())->where('status', 0)->count();
        $todayCheckOuts = AttendanceLog::whereDate('punched_at', today())->where('status', 1)->count();

        $pendingLeaves = LeaveApplication::where('status', 'pending')->count();
        $pendingBills = Bill::where('status', 'pending')->count();
        $pendingAdvances = Advance::where('status', 'pending')->count();
        $pendingMovements = MovementPass::where('status', 'pending')->count();

        $pendingLeaveAmount = LeaveApplication::where('status', 'pending')->sum('total_days');
        $pendingBillAmount = Bill::where('status', 'pending')->sum('amount');
        $pendingAdvanceAmount = Advance::where('status', 'pending')->sum('amount');

        $totalPayroll = SalaryStructure::sum(\DB::raw('basic_salary + house_rent + medical_allowance + conveyance_allowance + other_allowances'));
        $avgSalary = SalaryStructure::avg(\DB::raw('basic_salary + house_rent + medical_allowance + conveyance_allowance + other_allowances'));

        $maleCount = EmployeeProfile::where('gender', 'male')->count();
        $femaleCount = EmployeeProfile::where('gender', 'female')->count();

        $recentLeaves = LeaveApplication::with('employee')
            ->orderBy('created_at', 'desc')->take(5)->get();
        $recentBills = Bill::with('employee')
            ->orderBy('created_at', 'desc')->take(5)->get();

        $devicesCount = Device::count();
        $onlineDevicesCount = Device::get()->filter(fn ($d) => $d->isOnline())->count();
        $todayPunches = $todayLogs;
        $usersCount = ZktecoUser::count();

        return view('subscriber.hr-dashboard', compact(
            'tenant',
            'totalEmployees',
            'activeEmployees',
            'departments',
            'todayLogs',
            'todayCheckIns',
            'todayCheckOuts',
            'pendingLeaves',
            'pendingBills',
            'pendingAdvances',
            'pendingMovements',
            'pendingLeaveAmount',
            'pendingBillAmount',
            'pendingAdvanceAmount',
            'totalPayroll',
            'avgSalary',
            'maleCount',
            'femaleCount',
            'recentLeaves',
            'recentBills',
            'devicesCount',
            'onlineDevicesCount',
            'todayPunches',
            'usersCount'
        ));
    }
}
