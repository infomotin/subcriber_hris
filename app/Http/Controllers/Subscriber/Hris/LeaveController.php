<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
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

        // Generate default leave types if none exist to make data entry working out of the box
        if (LeaveType::count() === 0) {
            LeaveType::create(['tenant_id' => $tenant->id, 'name' => 'Casual Leave', 'code' => 'CL', 'days_per_year' => 14]);
            LeaveType::create(['tenant_id' => $tenant->id, 'name' => 'Sick Leave', 'code' => 'SL', 'days_per_year' => 10]);
            LeaveType::create(['tenant_id' => $tenant->id, 'name' => 'Earned Leave', 'code' => 'EL', 'days_per_year' => 15]);
        }

        $leaves = LeaveApplication::with(['employee.user', 'leaveType'])->orderBy('id', 'desc')->paginate(15);
        return view('subscriber.hris.leaves.index', compact('leaves'));
    }

    public function create()
    {
        $employees = EmployeeProfile::with('user')->get();
        $leaveTypes = LeaveType::get();
        return view('subscriber.hris.leaves.create', compact('employees', 'leaveTypes'));
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        $validated = $request->validate([
            'employee_profile_id' => 'required|exists:employee_profiles,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'status' => 'required|string'
        ]);

        // Calculate total days
        $start = \Carbon\Carbon::parse($validated['start_date']);
        $end = \Carbon\Carbon::parse($validated['end_date']);
        $totalDays = $start->diffInDays($end) + 1;

        LeaveApplication::create([
            'tenant_id' => $tenant->id,
            'employee_profile_id' => $validated['employee_profile_id'],
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $totalDays,
            'reason' => $validated['reason'],
            'status' => $validated['status']
        ]);

        return redirect()->route('subscriber.hris.leaves.index')->with('success', 'Leave application submitted successfully.');
    }

    public function edit(LeaveApplication $leave)
    {
        $employees = EmployeeProfile::with('user')->get();
        $leaveTypes = LeaveType::get();
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
            'status' => 'required|string'
        ]);

        $start = \Carbon\Carbon::parse($validated['start_date']);
        $end = \Carbon\Carbon::parse($validated['end_date']);
        $totalDays = $start->diffInDays($end) + 1;

        $leave->update(array_merge($validated, ['total_days' => $totalDays]));

        return redirect()->route('subscriber.hris.leaves.index')->with('success', 'Leave application updated successfully.');
    }

    public function destroy(LeaveApplication $leave)
    {
        $leave->delete();
        return redirect()->route('subscriber.hris.leaves.index')->with('success', 'Leave application deleted successfully.');
    }
}
