<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\EmployeeProfile;
use App\Models\Tenant;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        if ($tenant) {
            app()->instance('current_tenant_id', $tenant->id);
            session(['tenant_id' => $tenant->id]);
        }

        $leaves = LeaveApplication::with(['employee.user', 'leaveType', 'actionedBy'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('subscriber.hris.leaves.index', compact('leaves'));
    }

    public function show(LeaveApplication $leave)
    {
        return redirect()->route('subscriber.hris.leaves.index');
    }

    public function apply()
    {
        $employees = EmployeeProfile::with('user')->get();
        $leaveTypes = LeaveType::orderBy('name')->get();
        return view('subscriber.hris.leaves.apply', compact('employees', 'leaveTypes'));
    }

    public function getBalance(Request $request)
    {
        $emp = EmployeeProfile::with('leaveBalances.leaveType')->find($request->get('employee_profile_id'));
        if (!$emp) return response()->json([]);

        $types = LeaveType::orderBy('name')->get();
        $result = [];

        foreach ($types as $type) {
            $balance = $this->calculateBalance($emp, $type);
            $result[] = [
                'id' => $type->id,
                'name' => $type->name,
                'code' => $type->code,
                'allocated' => $balance['allocated'],
                'spent' => $balance['spent'],
                'available' => $balance['available'],
            ];
        }

        return response()->json($result);
    }

    private function calculateBalance($emp, $type): array
    {
        $year = now()->year;
        $balance = LeaveBalance::where('employee_profile_id', $emp->id)
            ->where('leave_type_id', $type->id)
            ->where('calendar_year', $year)
            ->first();

        if ($balance) {
            $available = ($balance->allocated_days + $balance->earned_days) - $balance->spent_days;
            return [
                'allocated' => (float) $balance->allocated_days,
                'spent' => (float) $balance->spent_days,
                'available' => max(0, (float) $available),
            ];
        }

        // Auto-calculate if no balance record exists
        $daysPerYear = (float) $type->days_per_year;
        if ($type->accrual_enabled && $emp->joining_date) {
            $joining = \Carbon\Carbon::parse($emp->joining_date);
            $monthsEmployed = max(1, $joining->diffInMonths(now()));
            $allocated = round(($daysPerYear / 12) * $monthsEmployed, 1);
        } else {
            $allocated = $daysPerYear;
        }

        $spent = LeaveApplication::where('employee_profile_id', $emp->id)
            ->where('leave_type_id', $type->id)
            ->whereIn('status', ['approved', 'pending'])
            ->whereYear('created_at', $year)
            ->sum('total_days');

        return [
            'allocated' => $allocated,
            'spent' => (float) $spent,
            'available' => max(0, $allocated - (float) $spent),
        ];
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();

        $validated = $request->validate([
            'employee_profile_id' => 'required|exists:employee_profiles,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        $start = \Carbon\Carbon::parse($validated['start_date']);
        $end = \Carbon\Carbon::parse($validated['end_date']);
        $totalDays = (int) $start->diffInDays($end) + 1;

        $emp = EmployeeProfile::find($validated['employee_profile_id']);
        $leaveType = LeaveType::find($validated['leave_type_id']);
        $balance = $this->calculateBalance($emp, $leaveType);

        if ($totalDays > $balance['available']) {
            return redirect()->back()
                ->withErrors(['error' => "Insufficient balance. Available: {$balance['available']} days, Requested: {$totalDays} days."])
                ->withInput();
        }

        $application = LeaveApplication::create([
            'tenant_id' => $tenant->id,
            'employee_profile_id' => $validated['employee_profile_id'],
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $totalDays,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        // Update or create leave balance spent days
        $year = now()->year;
        $lb = LeaveBalance::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'employee_profile_id' => $emp->id,
                'leave_type_id' => $leaveType->id,
                'calendar_year' => $year,
            ],
            [
                'allocated_days' => $balance['allocated'],
                'spent_days' => 0,
                'earned_days' => 0,
            ]
        );
        $lb->increment('spent_days', $totalDays);

        return redirect()->route('subscriber.hris.leaves.index')
            ->with('success', 'Leave application submitted for ' . ($emp->user->name ?? 'employee') . '.');
    }

    public function approve(LeaveApplication $leave)
    {
        if ($leave->status !== 'pending') {
            return redirect()->back()->withErrors(['error' => 'Only pending applications can be approved.']);
        }

        $leave->update([
            'status' => 'approved',
            'actioned_by' => auth()->id(),
            'action_remarks' => request('action_remarks'),
        ]);

        return redirect()->route('subscriber.hris.leaves.index')
            ->with('success', 'Leave application approved.');
    }

    public function reject(LeaveApplication $leave)
    {
        if ($leave->status !== 'pending') {
            return redirect()->back()->withErrors(['error' => 'Only pending applications can be rejected.']);
        }

        // Refund spent days
        $year = now()->year;
        $lb = LeaveBalance::where('employee_profile_id', $leave->employee_profile_id)
            ->where('leave_type_id', $leave->leave_type_id)
            ->where('calendar_year', $year)
            ->first();
        if ($lb) {
            $lb->decrement('spent_days', $leave->total_days);
        }

        $leave->update([
            'status' => 'rejected',
            'actioned_by' => auth()->id(),
            'action_remarks' => request('action_remarks', 'Application rejected.'),
        ]);

        return redirect()->route('subscriber.hris.leaves.index')
            ->with('success', 'Leave application rejected.');
    }

    public function edit(LeaveApplication $leave)
    {
        $employees = EmployeeProfile::with('user')->get();
        $leaveTypes = LeaveType::orderBy('name')->get();
        return view('subscriber.hris.leaves.edit', compact('leave', 'employees', 'leaveTypes'));
    }

    public function update(Request $request, LeaveApplication $leave)
    {
        $validated = $request->validate([
            'employee_profile_id' => 'required|exists:employee_profiles,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $start = \Carbon\Carbon::parse($validated['start_date']);
        $end = \Carbon\Carbon::parse($validated['end_date']);
        $totalDays = $start->diffInDays($end) + 1;

        $leave->update(array_merge($validated, ['total_days' => $totalDays]));

        return redirect()->route('subscriber.hris.leaves.index')->with('success', 'Leave application updated.');
    }

    public function destroy(LeaveApplication $leave)
    {
        if ($leave->status === 'pending') {
            $lb = LeaveBalance::where('employee_profile_id', $leave->employee_profile_id)
                ->where('leave_type_id', $leave->leave_type_id)
                ->where('calendar_year', now()->year)
                ->first();
            if ($lb) {
                $lb->decrement('spent_days', $leave->total_days);
            }
        }
        $leave->delete();
        return redirect()->route('subscriber.hris.leaves.index')->with('success', 'Leave application deleted.');
    }
}
