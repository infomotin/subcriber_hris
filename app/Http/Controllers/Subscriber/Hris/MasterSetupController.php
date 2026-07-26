<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\Gender;
use App\Models\Division;
use App\Models\District;
use App\Models\Thana;
use App\Models\EducationBoard;
use App\Models\Institution;
use App\Models\LeaveReason;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\EmployeeProfile;
use App\Models\SalaryRelation;
use App\Models\Tenant;
use Illuminate\Http\Request;

class MasterSetupController extends Controller
{
    public function index(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        if ($tenant) {
            app()->instance('current_tenant_id', $tenant->id);
            session(['tenant_id' => $tenant->id]);
        }

        // 1. Genders
        $genders = Gender::orderBy('name')->get();

        // 2. Geographic Hierarchy
        $divisions = Division::with('districts.thanas')->orderBy('name')->get();
        $allDivisions = Division::orderBy('name')->get();
        $allDistricts = District::with('division')->orderBy('name')->get();

        // 3. Education Boards & Institutions
        $boards = EducationBoard::orderBy('name')->get();
        $institutions = Institution::orderBy('name')->get();

        // 4. Leave Reasons & Balances
        $leaveReasons = LeaveReason::orderBy('reason')->get();
        $leaveTypes = LeaveType::get();
        $employees = EmployeeProfile::with('user')->get();
        $leaveBalances = LeaveBalance::with(['employee.user', 'leaveType'])->orderBy('id', 'desc')->get();

        // 5. Salary Relations
        $salaryRelations = SalaryRelation::get();
        $activeSalaryRelation = SalaryRelation::where('is_active', true)->first();

        // Selected tab
        $tab = $request->query('tab', 'sex');

        return view('subscriber.hris.master_setup.index', compact(
            'genders', 'divisions', 'allDivisions', 'allDistricts',
            'boards', 'institutions', 'leaveReasons', 'leaveTypes',
            'employees', 'leaveBalances', 'salaryRelations', 'activeSalaryRelation', 'tab'
        ));
    }

    public function store(Request $request, $type)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        
        switch ($type) {
            case 'sex':
                $validated = $request->validate(['name' => 'required|string|max:100']);
                Gender::create([
                    'tenant_id' => $tenant->id,
                    'name' => $validated['name']
                ]);
                $msg = 'Sex/Gender added successfully.';
                break;
            case 'division':
                $validated = $request->validate(['name' => 'required|string|max:100']);
                Division::create(['name' => $validated['name']]);
                $msg = 'Division added successfully.';
                break;
            case 'district':
                $validated = $request->validate([
                    'division_id' => 'required|exists:divisions,id',
                    'name' => 'required|string|max:100'
                ]);
                District::create($validated);
                $msg = 'District added successfully.';
                break;
            case 'thana':
                $validated = $request->validate([
                    'district_id' => 'required|exists:districts,id',
                    'name' => 'required|string|max:100'
                ]);
                Thana::create($validated);
                $msg = 'Thana/Upazila added successfully.';
                break;
            case 'board':
                $validated = $request->validate(['name' => 'required|string|max:100']);
                EducationBoard::create([
                    'tenant_id' => $tenant->id,
                    'name' => $validated['name']
                ]);
                $msg = 'Education Board added successfully.';
                break;
            case 'institution':
                $validated = $request->validate(['name' => 'required|string|max:255']);
                Institution::create([
                    'tenant_id' => $tenant->id,
                    'name' => $validated['name']
                ]);
                $msg = 'Institution added successfully.';
                break;
            case 'leave_reason':
                $validated = $request->validate(['reason' => 'required|string|max:255']);
                LeaveReason::create([
                    'tenant_id' => $tenant->id,
                    'reason' => $validated['reason']
                ]);
                $msg = 'Leave reason added successfully.';
                break;
            case 'leave_type':
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'code' => 'required|string|max:10',
                    'days_per_year' => 'required|numeric|min:0',
                    'accrual_enabled' => 'boolean',
                ]);
                LeaveType::create([
                    'tenant_id' => $tenant->id,
                    'name' => $validated['name'],
                    'code' => strtoupper($validated['code']),
                    'days_per_year' => $validated['days_per_year'],
                    'accrual_enabled' => $request->boolean('accrual_enabled'),
                ]);
                $msg = 'Leave type created successfully.';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid type specified.');
        }

        return redirect()->route('subscriber.hris.master.index', ['tab' => $type])->with('success', $msg);
    }

    public function destroy($type, $id)
    {
        switch ($type) {
            case 'sex':
                Gender::destroy($id);
                break;
            case 'division':
                Division::destroy($id);
                break;
            case 'district':
                District::destroy($id);
                break;
            case 'thana':
                Thana::destroy($id);
                break;
            case 'board':
                EducationBoard::destroy($id);
                break;
            case 'institution':
                Institution::destroy($id);
                break;
            case 'leave_reason':
                LeaveReason::destroy($id);
                break;
            case 'leave_balance':
                LeaveBalance::destroy($id);
                break;
            case 'leave_type':
                LeaveType::destroy($id);
                break;
            default:
                return redirect()->back()->with('error', 'Invalid type.');
        }

        return redirect()->route('subscriber.hris.master.index', ['tab' => $type])->with('success', 'Record deleted successfully.');
    }

    public function storeSalaryRelation(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'basic_percent' => 'required|numeric|min:0|max:100',
            'house_rent_percent' => 'required|numeric|min:0|max:100',
            'medical_percent' => 'required|numeric|min:0|max:100',
            'tada_percent' => 'required|numeric|min:0|max:100',
        ]);

        $total = $validated['basic_percent'] + $validated['house_rent_percent'] + $validated['medical_percent'] + $validated['tada_percent'];
        if (abs($total - 100.00) > 0.01) {
            return redirect()->back()->withInput()->with('error', 'The sum of all salary relation percentages must equal 100%. Current total: ' . $total . '%');
        }

        // Deactivate existing
        SalaryRelation::where('tenant_id', $tenant->id)->update(['is_active' => false]);

        SalaryRelation::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['name'],
            'basic_percent' => $validated['basic_percent'],
            'house_rent_percent' => $validated['house_rent_percent'],
            'medical_percent' => $validated['medical_percent'],
            'tada_percent' => $validated['tada_percent'],
            'is_active' => true
        ]);

        return redirect()->route('subscriber.hris.master.index', ['tab' => 'salary'])->with('success', 'Salary relation formula configured successfully.');
    }

    public function storeLeaveBalance(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();

        $validated = $request->validate([
            'employee_profile_id' => 'required|exists:employee_profiles,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'calendar_year' => 'required|integer',
            'allocated_days' => 'required|integer|min:0',
            'earned_days' => 'nullable|integer|min:0'
        ]);

        // Check if balance already exists
        $existing = LeaveBalance::where('employee_profile_id', $validated['employee_profile_id'])
            ->where('leave_type_id', $validated['leave_type_id'])
            ->where('calendar_year', $validated['calendar_year'])
            ->first();

        if ($existing) {
            $existing->update([
                'allocated_days' => $validated['allocated_days'],
                'earned_days' => $validated['earned_days'] ?? 0
            ]);
        } else {
            LeaveBalance::create([
                'tenant_id' => $tenant->id,
                'employee_profile_id' => $validated['employee_profile_id'],
                'leave_type_id' => $validated['leave_type_id'],
                'calendar_year' => $validated['calendar_year'],
                'allocated_days' => $validated['allocated_days'],
                'spent_days' => 0,
                'earned_days' => $validated['earned_days'] ?? 0
            ]);
        }

        return redirect()->route('subscriber.hris.master.index', ['tab' => 'leave_balance'])->with('success', 'Employee leave balance configured.');
    }
}
