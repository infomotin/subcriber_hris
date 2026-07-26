<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\EmployeeProfile;
use App\Models\Department;
use App\Models\Designation;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Gender;
use App\Models\Division;
use App\Models\EducationBoard;
use App\Models\Institution;
use App\Models\SalaryRelation;
use App\Models\WorkShift;
use App\Models\EmployeeExperience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        if ($tenant) {
            app()->instance('current_tenant_id', $tenant->id);
            session(['tenant_id' => $tenant->id]);
        }

        $employees = EmployeeProfile::with(['user', 'department', 'designation'])->orderBy('id', 'desc')->paginate(15);
        $departments = Department::orderBy('name', 'asc')->get();
        $designations = Designation::orderBy('title', 'asc')->get();
        return view('subscriber.hris.employees.index', compact('employees', 'departments', 'designations'));
    }

    public function create()
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        if ($tenant) {
            app()->instance('current_tenant_id', $tenant->id);
            session(['tenant_id' => $tenant->id]);
        }

        $departments = Department::orderBy('name', 'asc')->get();
        $designations = Designation::orderBy('title', 'asc')->get();
        $genders = Gender::orderBy('name', 'asc')->get();
        $divisions = Division::with('districts.thanas')->orderBy('name', 'asc')->get();
        $shifts = WorkShift::orderBy('name', 'asc')->get();
        $boards = EducationBoard::orderBy('name', 'asc')->get();
        $institutions = Institution::orderBy('name', 'asc')->get();
        $activeSalaryRelation = SalaryRelation::where('is_active', true)->first();

        return view('subscriber.hris.employees.create', compact(
            'departments', 'designations', 'genders', 'divisions',
            'shifts', 'boards', 'institutions', 'activeSalaryRelation'
        ));
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        
        $validated = $request->validate([
            // User login details
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            
            // Profile master details
            'employee_id' => 'required|string|unique:employee_profiles,employee_id',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'joining_date' => 'required|date',
            'gender' => 'required|string',
            'dob' => 'required|date',
            'phone_number' => 'required|string|max:20',
            'blood_group' => 'nullable|string|max:5',
            'status' => 'required|string',
            
            // Address details
            'address_line_1' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            
            // Bank details
            'bank_name' => 'required|string|max:255',
            'branch_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'routing_number' => 'nullable|string|max:50',
            'payment_mode' => 'required|string',

            // Salary structure details
            'basic_salary' => 'required|numeric|min:0',
            'house_rent' => 'required|numeric|min:0',
            'medical_allowance' => 'required|numeric|min:0',
            'conveyance_allowance' => 'required|numeric|min:0',
            'other_allowances' => 'nullable|numeric|min:0',
            'provident_fund_deduction' => 'nullable|numeric|min:0',
            'tax_deduction' => 'nullable|numeric|min:0',

            // Education details
            'degree_name' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'passing_year' => 'required|string|max:4',
            'result' => 'required|string|max:50',
            'certification_type' => 'nullable|string|max:100',

            // Experience details
            'company_name' => 'nullable|string|max:255',
            'prev_designation' => 'nullable|string|max:255',
            'exp_start_date' => 'nullable|date',
            'exp_end_date' => 'nullable|date',
            'job_description' => 'nullable|string'
        ]);

        DB::transaction(function () use ($validated, $tenant) {
            // 1. Create Login User
            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'])
            ]);

            // 2. Create Employee Profile
            $profile = EmployeeProfile::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'department_id' => $validated['department_id'],
                'designation_id' => $validated['designation_id'],
                'employee_id' => $validated['employee_id'],
                'joining_date' => $validated['joining_date'],
                'gender' => $validated['gender'],
                'dob' => $validated['dob'],
                'phone_number' => $validated['phone_number'],
                'blood_group' => $validated['blood_group'],
                'status' => $validated['status']
            ]);

            // 3. Create Address
            $profile->addresses()->create([
                'tenant_id' => $tenant->id,
                'type' => 'current',
                'address_line_1' => $validated['address_line_1'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'zip_code' => $validated['zip_code'],
                'country' => $validated['country'],
                'is_active' => true
            ]);

            // 4. Create Bank Info
            $profile->bankInfo()->create([
                'tenant_id' => $tenant->id,
                'bank_name' => $validated['bank_name'],
                'branch_name' => $validated['branch_name'],
                'account_name' => $validated['account_name'],
                'account_number' => $validated['account_number'],
                'routing_number' => $validated['routing_number'],
                'payment_mode' => $validated['payment_mode']
            ]);

            // 5. Create Salary Structure
            $profile->salaryStructure()->create([
                'tenant_id' => $tenant->id,
                'basic_salary' => $validated['basic_salary'],
                'house_rent' => $validated['house_rent'],
                'medical_allowance' => $validated['medical_allowance'],
                'conveyance_allowance' => $validated['conveyance_allowance'],
                'other_allowances' => $validated['other_allowances'] ?? 0,
                'provident_fund_deduction' => $validated['provident_fund_deduction'] ?? 0,
                'tax_deduction' => $validated['tax_deduction'] ?? 0
            ]);

            // 6. Create Education Info
            $profile->education()->create([
                'tenant_id' => $tenant->id,
                'degree_name' => $validated['degree_name'],
                'institution' => $validated['institution'],
                'passing_year' => $validated['passing_year'],
                'result' => $validated['result'],
                'certification_type' => $validated['certification_type'] ?? 'education'
            ]);

            // 7. Create Experience Info
            if (!empty($validated['company_name'])) {
                $profile->experiences()->create([
                    'tenant_id' => $tenant->id,
                    'company_name' => $validated['company_name'],
                    'designation' => $validated['prev_designation'] ?? 'N/A',
                    'start_date' => $validated['exp_start_date'] ?? now()->toDateString(),
                    'end_date' => $validated['exp_end_date'],
                    'job_description' => $validated['job_description']
                ]);
            }
        });

        return redirect()->route('subscriber.hris.employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(EmployeeProfile $employee)
    {
        return view('subscriber.hris.employees.show', compact('employee'));
    }

    public function edit(EmployeeProfile $employee)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        if ($tenant) {
            app()->instance('current_tenant_id', $tenant->id);
            session(['tenant_id' => $tenant->id]);
        }

        $departments = Department::orderBy('name', 'asc')->get();
        $designations = Designation::orderBy('title', 'asc')->get();
        $genders = Gender::orderBy('name', 'asc')->get();
        $divisions = Division::with('districts.thanas')->orderBy('name', 'asc')->get();
        $shifts = WorkShift::orderBy('name', 'asc')->get();
        $boards = EducationBoard::orderBy('name', 'asc')->get();
        $institutions = Institution::orderBy('name', 'asc')->get();
        $activeSalaryRelation = SalaryRelation::where('is_active', true)->first();

        // Eager load relations for pre-population
        $employee->load(['user', 'addresses', 'bankInfo', 'salaryStructure', 'education', 'experiences']);

        return view('subscriber.hris.employees.edit', compact(
            'employee', 'departments', 'designations', 'genders', 'divisions',
            'shifts', 'boards', 'institutions', 'activeSalaryRelation'
        ));
    }

    public function update(Request $request, EmployeeProfile $employee)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();

        $validated = $request->validate([
            // User login details
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->user_id,
            'password' => 'nullable|string|min:8',
            
            // Profile details
            'employee_id' => 'required|string|unique:employee_profiles,employee_id,' . $employee->id,
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'joining_date' => 'required|date',
            'gender' => 'required|string',
            'dob' => 'required|date',
            'phone_number' => 'required|string|max:20',
            'blood_group' => 'nullable|string|max:5',
            'status' => 'required|string',
            
            // Address details
            'address_line_1' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            
            // Bank details
            'bank_name' => 'required|string|max:255',
            'branch_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'routing_number' => 'nullable|string|max:50',
            'payment_mode' => 'required|string',

            // Salary structure details
            'basic_salary' => 'required|numeric|min:0',
            'house_rent' => 'required|numeric|min:0',
            'medical_allowance' => 'required|numeric|min:0',
            'conveyance_allowance' => 'required|numeric|min:0',
            'other_allowances' => 'nullable|numeric|min:0',
            'provident_fund_deduction' => 'nullable|numeric|min:0',
            'tax_deduction' => 'nullable|numeric|min:0',

            // Education details
            'degree_name' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'passing_year' => 'required|string|max:4',
            'result' => 'required|string|max:50',
            'certification_type' => 'nullable|string|max:100',

            // Experience details
            'company_name' => 'nullable|string|max:255',
            'prev_designation' => 'nullable|string|max:255',
            'exp_start_date' => 'nullable|date',
            'exp_end_date' => 'nullable|date',
            'job_description' => 'nullable|string'
        ]);

        DB::transaction(function () use ($validated, $employee, $tenant) {
            // 1. Update Login User
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];
            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }
            $employee->user->update($userData);

            // 2. Update Employee Profile
            $employee->update([
                'department_id' => $validated['department_id'],
                'designation_id' => $validated['designation_id'],
                'employee_id' => $validated['employee_id'],
                'joining_date' => $validated['joining_date'],
                'gender' => $validated['gender'],
                'dob' => $validated['dob'],
                'phone_number' => $validated['phone_number'],
                'blood_group' => $validated['blood_group'],
                'status' => $validated['status']
            ]);

            // 3. Update Address
            $currentAddress = $employee->addresses()->where('type', 'current')->first();
            if ($currentAddress) {
                $currentAddress->update([
                    'address_line_1' => $validated['address_line_1'],
                    'city' => $validated['city'],
                    'state' => $validated['state'],
                    'zip_code' => $validated['zip_code'],
                    'country' => $validated['country']
                ]);
            } else {
                $employee->addresses()->create([
                    'tenant_id' => $tenant->id,
                    'type' => 'current',
                    'address_line_1' => $validated['address_line_1'],
                    'city' => $validated['city'],
                    'state' => $validated['state'],
                    'zip_code' => $validated['zip_code'],
                    'country' => $validated['country'],
                    'is_active' => true
                ]);
            }

            // 4. Update Bank Info
            $bankInfo = $employee->bankInfo;
            if ($bankInfo) {
                $bankInfo->update([
                    'bank_name' => $validated['bank_name'],
                    'branch_name' => $validated['branch_name'],
                    'account_name' => $validated['account_name'],
                    'account_number' => $validated['account_number'],
                    'routing_number' => $validated['routing_number'],
                    'payment_mode' => $validated['payment_mode']
                ]);
            } else {
                $employee->bankInfo()->create([
                    'tenant_id' => $tenant->id,
                    'bank_name' => $validated['bank_name'],
                    'branch_name' => $validated['branch_name'],
                    'account_name' => $validated['account_name'],
                    'account_number' => $validated['account_number'],
                    'routing_number' => $validated['routing_number'],
                    'payment_mode' => $validated['payment_mode']
                ]);
            }

            // 5. Update Salary Structure
            $salary = $employee->salaryStructure;
            if ($salary) {
                $salary->update([
                    'basic_salary' => $validated['basic_salary'],
                    'house_rent' => $validated['house_rent'],
                    'medical_allowance' => $validated['medical_allowance'],
                    'conveyance_allowance' => $validated['conveyance_allowance'],
                    'other_allowances' => $validated['other_allowances'] ?? 0,
                    'provident_fund_deduction' => $validated['provident_fund_deduction'] ?? 0,
                    'tax_deduction' => $validated['tax_deduction'] ?? 0
                ]);
            } else {
                $employee->salaryStructure()->create([
                    'tenant_id' => $tenant->id,
                    'basic_salary' => $validated['basic_salary'],
                    'house_rent' => $validated['house_rent'],
                    'medical_allowance' => $validated['medical_allowance'],
                    'conveyance_allowance' => $validated['conveyance_allowance'],
                    'other_allowances' => $validated['other_allowances'] ?? 0,
                    'provident_fund_deduction' => $validated['provident_fund_deduction'] ?? 0,
                    'tax_deduction' => $validated['tax_deduction'] ?? 0
                ]);
            }

            // 6. Update Education Info
            $education = $employee->education()->first();
            if ($education) {
                $education->update([
                    'degree_name' => $validated['degree_name'],
                    'institution' => $validated['institution'],
                    'passing_year' => $validated['passing_year'],
                    'result' => $validated['result'],
                    'certification_type' => $validated['certification_type'] ?? 'education'
                ]);
            } else {
                $employee->education()->create([
                    'tenant_id' => $tenant->id,
                    'degree_name' => $validated['degree_name'],
                    'institution' => $validated['institution'],
                    'passing_year' => $validated['passing_year'],
                    'result' => $validated['result'],
                    'certification_type' => $validated['certification_type'] ?? 'education'
                ]);
            }

            // 7. Update Experience Info
            $experience = $employee->experiences()->first();
            if (!empty($validated['company_name'])) {
                if ($experience) {
                    $experience->update([
                        'company_name' => $validated['company_name'],
                        'designation' => $validated['prev_designation'] ?? 'N/A',
                        'start_date' => $validated['exp_start_date'] ?? now()->toDateString(),
                        'end_date' => $validated['exp_end_date'],
                        'job_description' => $validated['job_description']
                    ]);
                } else {
                    $employee->experiences()->create([
                        'tenant_id' => $tenant->id,
                        'company_name' => $validated['company_name'],
                        'designation' => $validated['prev_designation'] ?? 'N/A',
                        'start_date' => $validated['exp_start_date'] ?? now()->toDateString(),
                        'end_date' => $validated['exp_end_date'],
                        'job_description' => $validated['job_description']
                    ]);
                }
            } elseif ($experience) {
                $experience->delete();
            }
        });

        return redirect()->route('subscriber.hris.employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(EmployeeProfile $employee)
    {
        // Deleting employee profile and associated user
        DB::transaction(function () use ($employee) {
            $userId = $employee->user_id;
            $employee->delete();
            User::where('id', $userId)->delete();
        });

        return redirect()->route('subscriber.hris.employees.index')->with('success', 'Employee deleted successfully.');
    }
}
