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
use App\Models\EmployeeDocument;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        if ($tenant) {
            app()->instance('current_tenant_id', $tenant->id);
            session(['tenant_id' => $tenant->id]);
        }

        $query = EmployeeProfile::with(['user', 'department', 'designation', 'shift', 'verifications']);

        // Search
        $search = request('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filters
        if (request('department_id')) {
            $query->where('department_id', request('department_id'));
        }
        if (request('designation_id')) {
            $query->where('designation_id', request('designation_id'));
        }
        if (request('status')) {
            $query->where('status', request('status'));
        }
        if (request('shift_id')) {
            $query->where('shift_id', request('shift_id'));
        }

        $employees = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $departments = Department::orderBy('name', 'asc')->get();
        $designations = Designation::orderBy('title', 'asc')->get();
        $shifts = WorkShift::orderBy('name', 'asc')->get();
        return view('subscriber.hris.employees.index', compact('employees', 'departments', 'designations', 'shifts'));
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
            'shift_id' => 'nullable|exists:work_shifts,id',
            'joining_date' => 'required|date',
            'gender' => 'required|string',
            'dob' => 'required|date',
            'phone_number' => 'required|string|max:20',
            'blood_group' => 'nullable|string|max:5',
            'status' => 'required|string',
            // Personal info
            'nid' => 'nullable|string|max:30',
            'birth_certificate' => 'nullable|string|max:30',
            'religion' => 'nullable|string|max:50',
            'marital_status' => 'nullable|string|max:20',
            'father_name' => 'nullable|string|max:100',
            'father_occupation' => 'nullable|string|max:100',
            'mother_name' => 'nullable|string|max:100',
            'mother_occupation' => 'nullable|string|max:100',
            'guardian_name' => 'nullable|string|max:100',
            'guardian_relation' => 'nullable|string|max:50',
            'guardian_phone' => 'nullable|string|max:20',
            
            // Current Address details
            'address_line_1' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            
            // Present/Permanent Address
            'permanent_address_line_1' => 'nullable|string|max:255',
            'permanent_city' => 'nullable|string|max:100',
            'permanent_state' => 'nullable|string|max:100',
            'permanent_zip_code' => 'nullable|string|max:20',
            'permanent_country' => 'nullable|string|max:100',

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

            // Education details (array of degrees)
            'education' => 'nullable|array',
            'education.*.degree_name' => 'required|string|max:255',
            'education.*.institution' => 'required|string|max:255',
            'education.*.passing_year' => 'required|string|max:4',
            'education.*.result' => 'required|string|max:50',
            'education.*.certification_type' => 'nullable|string|max:100',

            // Family / Dependents
            'dependents' => 'nullable|array',
            'dependents.*.name' => 'required|string|max:255',
            'dependents.*.relationship' => 'required|string|max:100',
            'dependents.*.phone' => 'nullable|string|max:20',

            // Nominees
            'nominees' => 'nullable|array',
            'nominees.*.name' => 'required|string|max:255',
            'nominees.*.relationship' => 'required|string|max:100',
            'nominees.*.share_percentage' => 'nullable|numeric|min:0|max:100',
            'nominees.*.identity_document_type' => 'nullable|string|max:50',
            'nominees.*.identity_document_number' => 'nullable|string|max:50',

            // Experience details (array of rows)
            'experiences' => 'nullable|array',
            'experiences.*.company_name' => 'nullable|string|max:255',
            'experiences.*.designation' => 'nullable|string|max:255',
            'experiences.*.start_date' => 'nullable|date',
            'experiences.*.end_date' => 'nullable|date',
            'experiences.*.job_description' => 'nullable|string',

            // Document uploads
            'doc_nid' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'doc_religion' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'doc_noc' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'doc_police_clearance' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'education_doc' => 'nullable|array',
            'education_doc.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'experience_doc' => 'nullable|array',
            'experience_doc.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        DB::transaction(function () use ($validated, $tenant, $request) {
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
                'shift_id' => $validated['shift_id'] ?? null,
                'employee_id' => $validated['employee_id'],
                'joining_date' => $validated['joining_date'],
                'gender' => $validated['gender'],
                'dob' => $validated['dob'],
                'phone_number' => $validated['phone_number'],
                'blood_group' => $validated['blood_group'],
                'status' => $validated['status'],
                'nid' => $validated['nid'] ?? null,
                'birth_certificate' => $validated['birth_certificate'] ?? null,
                'religion' => $validated['religion'] ?? null,
                'marital_status' => $validated['marital_status'] ?? null,
                'father_name' => $validated['father_name'] ?? null,
                'father_occupation' => $validated['father_occupation'] ?? null,
                'mother_name' => $validated['mother_name'] ?? null,
                'mother_occupation' => $validated['mother_occupation'] ?? null,
                'guardian_name' => $validated['guardian_name'] ?? null,
                'guardian_relation' => $validated['guardian_relation'] ?? null,
                'guardian_phone' => $validated['guardian_phone'] ?? null,
            ]);

            // 3. Create Current Address
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

            // 3b. Create Permanent/Present Address
            if (!empty($validated['permanent_address_line_1'])) {
                $profile->addresses()->create([
                    'tenant_id' => $tenant->id,
                    'type' => 'permanent',
                    'address_line_1' => $validated['permanent_address_line_1'],
                    'city' => $validated['permanent_city'] ?? '',
                    'state' => $validated['permanent_state'] ?? '',
                    'zip_code' => $validated['permanent_zip_code'] ?? '',
                    'country' => $validated['permanent_country'] ?? 'Bangladesh',
                    'is_active' => true
                ]);
            }

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

            // 6. Create Education Info (multiple degrees)
            if (!empty($validated['education'])) {
                foreach ($validated['education'] as $idx => $eduData) {
                    $edu = $profile->education()->create([
                        'tenant_id' => $tenant->id,
                        'degree_name' => $eduData['degree_name'],
                        'institution' => $eduData['institution'],
                        'passing_year' => $eduData['passing_year'],
                        'result' => $eduData['result'],
                        'certification_type' => $eduData['certification_type'] ?? 'education'
                    ]);
                    if ($request->hasFile("education_doc.{$idx}")) {
                        $fileData = $this->storeFileUpload($request->file("education_doc.{$idx}"), 'Certificate', 'education');
                        $edu->documents()->create(array_merge($fileData, ['tenant_id' => $tenant->id]));
                    }
                }
            }

            // 6b. Create Family / Dependents
            if (!empty($validated['dependents'])) {
                foreach ($validated['dependents'] as $dep) {
                    $profile->dependents()->create([
                        'tenant_id' => $tenant->id,
                        'name' => $dep['name'],
                        'relationship' => $dep['relationship'],
                        'contact_number' => $dep['phone'] ?? null,
                    ]);
                }
            }

            // 6c. Create Nominees
            if (!empty($validated['nominees'])) {
                foreach ($validated['nominees'] as $nom) {
                    $profile->nominees()->create([
                        'tenant_id' => $tenant->id,
                        'name' => $nom['name'],
                        'relationship' => $nom['relationship'],
                        'share_percentage' => $nom['share_percentage'] ?? 100,
                        'identity_document_type' => $nom['identity_document_type'] ?? null,
                        'identity_document_number' => $nom['identity_document_number'] ?? null,
                    ]);
                }
            }

            // 7. Create Experience Info (multiple rows)
            if (!empty($validated['experiences'])) {
                foreach ($validated['experiences'] as $idx => $expData) {
                    if (!empty($expData['company_name'])) {
                        $exp = $profile->experiences()->create([
                            'tenant_id' => $tenant->id,
                            'company_name' => $expData['company_name'],
                            'designation' => $expData['designation'] ?? 'N/A',
                            'start_date' => $expData['start_date'] ?? now()->toDateString(),
                            'end_date' => $expData['end_date'] ?? null,
                            'job_description' => $expData['job_description'] ?? null,
                        ]);
                        if ($request->hasFile("experience_doc.{$idx}")) {
                            $fileData = $this->storeFileUpload($request->file("experience_doc.{$idx}"), 'Experience Letter', 'experience');
                            $exp->documents()->create(array_merge($fileData, ['tenant_id' => $tenant->id]));
                        }
                    }
                }
            }

            // 8. Attach profile-level documents
            $profileDocs = [
                'doc_nid' => 'NID',
                'doc_religion' => 'Religion Document',
                'doc_noc' => 'NOC',
                'doc_police_clearance' => 'Police Clearance',
            ];
            foreach ($profileDocs as $field => $label) {
                if ($request->hasFile($field)) {
                    $fileData = $this->storeFileUpload($request->file($field), $label, 'profile');
                    $profile->documents()->create(array_merge($fileData, ['tenant_id' => $tenant->id]));
                }
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
        $employee->load(['user', 'addresses', 'bankInfo', 'salaryStructure', 'education', 'experiences', 'dependents', 'nominees']);

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
            'shift_id' => 'nullable|exists:work_shifts,id',
            'joining_date' => 'required|date',
            'gender' => 'required|string',
            'dob' => 'required|date',
            'phone_number' => 'required|string|max:20',
            'blood_group' => 'nullable|string|max:5',
            'status' => 'required|string',
            // Personal info
            'nid' => 'nullable|string|max:30',
            'birth_certificate' => 'nullable|string|max:30',
            'religion' => 'nullable|string|max:50',
            'marital_status' => 'nullable|string|max:20',
            'father_name' => 'nullable|string|max:100',
            'father_occupation' => 'nullable|string|max:100',
            'mother_name' => 'nullable|string|max:100',
            'mother_occupation' => 'nullable|string|max:100',
            'guardian_name' => 'nullable|string|max:100',
            'guardian_relation' => 'nullable|string|max:50',
            'guardian_phone' => 'nullable|string|max:20',
            
            // Current Address details
            'address_line_1' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            
            // Present/Permanent Address
            'permanent_address_line_1' => 'nullable|string|max:255',
            'permanent_city' => 'nullable|string|max:100',
            'permanent_state' => 'nullable|string|max:100',
            'permanent_zip_code' => 'nullable|string|max:20',
            'permanent_country' => 'nullable|string|max:100',

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

            // Education details (array of degrees)
            'education' => 'nullable|array',
            'education.*.degree_name' => 'required|string|max:255',
            'education.*.institution' => 'required|string|max:255',
            'education.*.passing_year' => 'required|string|max:4',
            'education.*.result' => 'required|string|max:50',
            'education.*.certification_type' => 'nullable|string|max:100',

            // Family / Dependents
            'dependents' => 'nullable|array',
            'dependents.*.name' => 'required|string|max:255',
            'dependents.*.relationship' => 'required|string|max:100',
            'dependents.*.phone' => 'nullable|string|max:20',

            // Nominees
            'nominees' => 'nullable|array',
            'nominees.*.name' => 'required|string|max:255',
            'nominees.*.relationship' => 'required|string|max:100',
            'nominees.*.share_percentage' => 'nullable|numeric|min:0|max:100',
            'nominees.*.identity_document_type' => 'nullable|string|max:50',
            'nominees.*.identity_document_number' => 'nullable|string|max:50',

            // Experience details (array of rows)
            'experiences' => 'nullable|array',
            'experiences.*.company_name' => 'nullable|string|max:255',
            'experiences.*.designation' => 'nullable|string|max:255',
            'experiences.*.start_date' => 'nullable|date',
            'experiences.*.end_date' => 'nullable|date',
            'experiences.*.job_description' => 'nullable|string',

            // Document uploads
            'doc_nid' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'doc_religion' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'doc_noc' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'doc_police_clearance' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'education_doc' => 'nullable|array',
            'education_doc.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'experience_doc' => 'nullable|array',
            'experience_doc.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        DB::transaction(function () use ($validated, $employee, $tenant, $request) {
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
                'shift_id' => $validated['shift_id'] ?? null,
                'employee_id' => $validated['employee_id'],
                'joining_date' => $validated['joining_date'],
                'gender' => $validated['gender'],
                'dob' => $validated['dob'],
                'phone_number' => $validated['phone_number'],
                'blood_group' => $validated['blood_group'],
                'status' => $validated['status'],
                'nid' => $validated['nid'] ?? null,
                'birth_certificate' => $validated['birth_certificate'] ?? null,
                'religion' => $validated['religion'] ?? null,
                'marital_status' => $validated['marital_status'] ?? null,
                'father_name' => $validated['father_name'] ?? null,
                'father_occupation' => $validated['father_occupation'] ?? null,
                'mother_name' => $validated['mother_name'] ?? null,
                'mother_occupation' => $validated['mother_occupation'] ?? null,
                'guardian_name' => $validated['guardian_name'] ?? null,
                'guardian_relation' => $validated['guardian_relation'] ?? null,
                'guardian_phone' => $validated['guardian_phone'] ?? null,
            ]);

            // 3. Update Current Address
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

            // 3b. Update Permanent/Present Address
            $permAddress = $employee->addresses()->where('type', 'permanent')->first();
            if (!empty($validated['permanent_address_line_1'])) {
                if ($permAddress) {
                    $permAddress->update([
                        'address_line_1' => $validated['permanent_address_line_1'],
                        'city' => $validated['permanent_city'] ?? '',
                        'state' => $validated['permanent_state'] ?? '',
                        'zip_code' => $validated['permanent_zip_code'] ?? '',
                        'country' => $validated['permanent_country'] ?? 'Bangladesh'
                    ]);
                } else {
                    $employee->addresses()->create([
                        'tenant_id' => $tenant->id,
                        'type' => 'permanent',
                        'address_line_1' => $validated['permanent_address_line_1'],
                        'city' => $validated['permanent_city'] ?? '',
                        'state' => $validated['permanent_state'] ?? '',
                        'zip_code' => $validated['permanent_zip_code'] ?? '',
                        'country' => $validated['permanent_country'] ?? 'Bangladesh',
                        'is_active' => true
                    ]);
                }
            } elseif ($permAddress) {
                $permAddress->delete();
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

            // 6. Update Education Info (replace all)
            foreach ($employee->education as $edu) {
                foreach ($edu->documents as $doc) {
                    $this->deleteDocument($doc);
                }
                $edu->delete();
            }
            if (!empty($validated['education'])) {
                foreach ($validated['education'] as $idx => $eduData) {
                    $edu = $employee->education()->create([
                        'tenant_id' => $tenant->id,
                        'degree_name' => $eduData['degree_name'],
                        'institution' => $eduData['institution'],
                        'passing_year' => $eduData['passing_year'],
                        'result' => $eduData['result'],
                        'certification_type' => $eduData['certification_type'] ?? 'education'
                    ]);
                    if ($request->hasFile("education_doc.{$idx}")) {
                        $fileData = $this->storeFileUpload($request->file("education_doc.{$idx}"), 'Certificate', 'education');
                        $edu->documents()->create(array_merge($fileData, ['tenant_id' => $tenant->id]));
                    }
                }
            }

            // 6b. Update Dependents (replace all)
            $employee->dependents()->delete();
            if (!empty($validated['dependents'])) {
                foreach ($validated['dependents'] as $dep) {
                    $employee->dependents()->create([
                        'tenant_id' => $tenant->id,
                        'name' => $dep['name'],
                        'relationship' => $dep['relationship'],
                        'contact_number' => $dep['phone'] ?? null,
                    ]);
                }
            }

            // 6c. Update Nominees (replace all)
            $employee->nominees()->delete();
            if (!empty($validated['nominees'])) {
                foreach ($validated['nominees'] as $nom) {
                    $employee->nominees()->create([
                        'tenant_id' => $tenant->id,
                        'name' => $nom['name'],
                        'relationship' => $nom['relationship'],
                        'share_percentage' => $nom['share_percentage'] ?? 100,
                        'identity_document_type' => $nom['identity_document_type'] ?? null,
                        'identity_document_number' => $nom['identity_document_number'] ?? null,
                    ]);
                }
            }

            // 7. Update Experience Info (replace all)
            foreach ($employee->experiences as $exp) {
                foreach ($exp->documents as $doc) {
                    $this->deleteDocument($doc);
                }
                $exp->delete();
            }
            if (!empty($validated['experiences'])) {
                foreach ($validated['experiences'] as $idx => $expData) {
                    if (!empty($expData['company_name'])) {
                        $exp = $employee->experiences()->create([
                            'tenant_id' => $tenant->id,
                            'company_name' => $expData['company_name'],
                            'designation' => $expData['designation'] ?? 'N/A',
                            'start_date' => $expData['start_date'] ?? now()->toDateString(),
                            'end_date' => $expData['end_date'] ?? null,
                            'job_description' => $expData['job_description'] ?? null,
                        ]);
                        if ($request->hasFile("experience_doc.{$idx}")) {
                            $fileData = $this->storeFileUpload($request->file("experience_doc.{$idx}"), 'Experience Letter', 'experience');
                            $exp->documents()->create(array_merge($fileData, ['tenant_id' => $tenant->id]));
                        }
                    }
                }
            }

            // 8. Update profile-level documents (replace on new upload)
            $profileDocs = [
                'doc_nid' => 'NID',
                'doc_religion' => 'Religion Document',
                'doc_noc' => 'NOC',
                'doc_police_clearance' => 'Police Clearance',
            ];
            foreach ($profileDocs as $field => $label) {
                if ($request->hasFile($field)) {
                    foreach ($employee->documents()->where('label', $label)->get() as $oldDoc) {
                        $this->deleteDocument($oldDoc);
                    }
                    $fileData = $this->storeFileUpload($request->file($field), $label, 'profile');
                    $employee->documents()->create(array_merge($fileData, ['tenant_id' => $tenant->id]));
                }
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

    private function deleteDocument(EmployeeDocument $doc): void
    {
        Storage::disk('public')->delete($doc->file_path);
        $doc->delete();
    }

    private function storeFileUpload($file, string $label, string $subdir = 'general'): array
    {
        $originalName = $file->getClientOriginalName();
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs("employee_documents/{$subdir}", $filename, 'public');

        return [
            'label' => $label,
            'file_path' => $path,
            'original_name' => $originalName,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ];
    }
}
