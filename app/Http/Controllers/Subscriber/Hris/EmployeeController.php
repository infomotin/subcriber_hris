<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\EmployeeProfile;
use App\Models\Department;
use App\Models\Designation;
use App\Models\User;
use App\Models\Tenant;
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
        return view('subscriber.hris.employees.index', compact('employees'));
    }

    public function create()
    {
        $departments = Department::orderBy('name', 'asc')->get();
        $designations = Designation::orderBy('title', 'asc')->get();
        return view('subscriber.hris.employees.create', compact('departments', 'designations'));
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
            'payment_mode' => 'required|string'
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
        });

        return redirect()->route('subscriber.hris.employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(EmployeeProfile $employee)
    {
        return view('subscriber.hris.employees.show', compact('employee'));
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
