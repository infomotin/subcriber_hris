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
        return view('subscriber.adms.endpoint', $data);
    }

    public function admsPunchLogs()
    {
        $data = $this->prepareDashboardData();
        return view('subscriber.adms.punch-logs', $data);
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

    public function stats()
    {
        $user = auth()->user();
        $tenant = $user?->tenant ?? Tenant::first();
        if (! $tenant) {
            return response()->json(['error' => 'No tenant'], 404);
        }

        app()->instance('current_tenant_id', $tenant->id);

        $todayPunches = AttendanceLog::whereDate('punched_at', today())->count();

        $recentLogs = AttendanceLog::with(['device', 'zktecoUser'])
            ->orderBy('punched_at', 'desc')
            ->take(10)
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
