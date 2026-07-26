<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use App\Models\EmployeeProfile;
use App\Models\Gender;
use App\Models\Division;
use App\Models\District;
use App\Models\Thana;
use App\Models\EducationBoard;
use App\Models\Institution;
use App\Models\SalaryRelation;
use App\Models\WorkShift;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class HrisEmployeeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Get first tenant & role
        $tenant = Tenant::first();
        if (!$tenant) {
            $tenant = Tenant::create(['name' => 'Demo SaaS Organization', 'tenant_token' => 'DEMO_TOKEN_999']);
        }

        $employeeRole = Role::firstOrCreate(['name' => 'Employee']);

        // 2. Seed Departments
        $depts = ['Human Resources', 'Information Technology', 'Finance & Accounts', 'Operations & Support'];
        $createdDepts = [];
        foreach ($depts as $deptName) {
            $createdDepts[] = Department::firstOrCreate([
                'tenant_id' => $tenant->id,
                'name' => $deptName
            ], [
                'code' => strtoupper(substr($deptName, 0, 3)) . rand(10, 99)
            ]);
        }

        // 3. Seed Designations
        $desigs = [
            'HR Manager' => 'G1',
            'Senior Software Engineer' => 'G2',
            'Chief Technology Officer' => 'G1',
            'Senior Accountant' => 'G3',
            'Support Executive' => 'G4'
        ];
        $createdDesigs = [];
        foreach ($desigs as $title => $grade) {
            $createdDesigs[$title] = Designation::firstOrCreate([
                'tenant_id' => $tenant->id,
                'title' => $title
            ], [
                'grade' => $grade
            ]);
        }

        // 4. Query Setup Metadata
        $maleGender = Gender::where('tenant_id', $tenant->id)->where('name', 'Male')->first();
        $femaleGender = Gender::where('tenant_id', $tenant->id)->where('name', 'Female')->first();
        
        $dhakaDiv = Division::where('name', 'Dhaka')->first();
        $dhakaDist = District::where('division_id', $dhakaDiv?->id)->where('name', 'Dhaka')->first();
        $mirpurThana = Thana::where('district_id', $dhakaDist?->id)->where('name', 'Mirpur')->first();
        $uttaraThana = Thana::where('district_id', $dhakaDist?->id)->where('name', 'Uttara')->first();

        $activeSalaryRelation = SalaryRelation::where('tenant_id', $tenant->id)->where('is_active', true)->first();
        
        // Seed default shifts
        $shift = WorkShift::firstOrCreate([
            'tenant_id' => $tenant->id,
            'name' => 'Regular Morning Shift'
        ], [
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'late_buffer_time' => '09:15:00'
        ]);

        // 5. Seed 5 Employees
        $employeeData = [
            [
                'name' => 'Adnan Rahman',
                'email' => 'adnan@amds.test',
                'employee_id' => 'EMP2026001',
                'title' => 'Senior Software Engineer',
                'dept' => 'Information Technology',
                'gender' => 'Male',
                'dob' => '1992-05-15',
                'phone' => '+8801711223344',
                'blood' => 'O+',
                'gross' => 80000.00,
                'degree' => 'Bachelor of Science (B.Sc.)',
                'inst' => 'Dhaka University',
                'ex_company' => 'Cefalo Bangladesh',
                'ex_desig' => 'Software Engineer'
            ],
            [
                'name' => 'Nusrat Jahan',
                'email' => 'nusrat@amds.test',
                'employee_id' => 'EMP2026002',
                'title' => 'HR Manager',
                'dept' => 'Human Resources',
                'gender' => 'Female',
                'dob' => '1994-08-22',
                'phone' => '+8801811223344',
                'blood' => 'A+',
                'gross' => 60000.00,
                'degree' => 'Master of Business Administration (MBA)',
                'inst' => 'North South University',
                'ex_company' => 'Therap BD',
                'ex_desig' => 'HR Associate'
            ],
            [
                'name' => 'Tanvir Ahmed',
                'email' => 'tanvir@amds.test',
                'employee_id' => 'EMP2026003',
                'title' => 'Chief Technology Officer',
                'dept' => 'Information Technology',
                'gender' => 'Male',
                'dob' => '1988-02-10',
                'phone' => '+8801911223344',
                'blood' => 'B+',
                'gross' => 150000.00,
                'degree' => 'Master of Science (M.Sc.)',
                'inst' => 'BUET',
                'ex_company' => 'Brain Station 23',
                'ex_desig' => 'Tech Lead'
            ],
            [
                'name' => 'Fahmida Chowdhury',
                'email' => 'fahmida@amds.test',
                'employee_id' => 'EMP2026004',
                'title' => 'Senior Accountant',
                'dept' => 'Finance & Accounts',
                'gender' => 'Female',
                'dob' => '1995-11-30',
                'phone' => '+8801511223344',
                'blood' => 'AB+',
                'gross' => 50000.00,
                'degree' => 'Bachelor of Business Administration (BBA)',
                'inst' => 'Dhaka University',
                'ex_company' => 'Kazi Farms Group',
                'ex_desig' => 'Accounts Officer'
            ],
            [
                'name' => 'Sajid Islam',
                'email' => 'sajid@amds.test',
                'employee_id' => 'EMP2026005',
                'title' => 'Support Executive',
                'dept' => 'Operations & Support',
                'gender' => 'Male',
                'dob' => '1996-04-05',
                'phone' => '+8801611223344',
                'blood' => 'O-',
                'gross' => 30000.00,
                'degree' => 'Diploma in Engineering',
                'inst' => 'Dhaka Polytechnic Institute',
                'ex_company' => 'Link3 Technologies',
                'ex_desig' => 'Support Technician'
            ]
        ];

        foreach ($employeeData as $emp) {
            // Create user
            $user = User::firstOrCreate(
                ['email' => $emp['email']],
                [
                    'tenant_id' => $tenant->id,
                    'name' => $emp['name'],
                    'password' => Hash::make('password')
                ]
            );
            $user->syncRoles([$employeeRole]);

            // Create profile
            $dept = collect($createdDepts)->first(fn($d) => $d->name === $emp['dept']);
            $desig = $createdDesigs[$emp['title']] ?? null;

            $profile = EmployeeProfile::firstOrCreate(
                ['employee_id' => $emp['employee_id']],
                [
                    'tenant_id' => $tenant->id,
                    'user_id' => $user->id,
                    'department_id' => $dept?->id,
                    'designation_id' => $desig?->id,
                    'joining_date' => '2025-01-01',
                    'gender' => $emp['gender'],
                    'dob' => $emp['dob'],
                    'phone_number' => $emp['phone'],
                    'blood_group' => $emp['blood'],
                    'status' => 'active'
                ]
            );

            // Create address
            $profile->addresses()->firstOrCreate(
                ['type' => 'current'],
                [
                    'tenant_id' => $tenant->id,
                    'address_line_1' => 'Plot ' . rand(10, 100) . ', Road ' . rand(1, 15),
                    'city' => ($profile->id % 2 == 0) ? ($uttaraThana?->name . ', ' . $dhakaDist?->name) : ($mirpurThana?->name . ', ' . $dhakaDist?->name),
                    'state' => $dhakaDiv?->name ?? 'Dhaka',
                    'zip_code' => '12' . rand(10, 99),
                    'country' => 'Bangladesh',
                    'is_active' => true
                ]
            );

            // Create bank details
            $profile->bankInfo()->firstOrCreate(
                ['employee_profile_id' => $profile->id],
                [
                    'tenant_id' => $tenant->id,
                    'bank_name' => 'Dutch-Bangla Bank Limited',
                    'branch_name' => 'Dhanmondi Branch',
                    'account_name' => $emp['name'],
                    'account_number' => '102298' . rand(100000, 999999),
                    'routing_number' => '090261984',
                    'payment_mode' => 'bank_transfer'
                ]
            );

            // Create salary structure (based on active SalaryRelation formula)
            $basic = $emp['gross'] * 0.50;
            $house = $emp['gross'] * 0.25;
            $med = $emp['gross'] * 0.10;
            $tada = $emp['gross'] * 0.15;

            if ($activeSalaryRelation) {
                $basic = $emp['gross'] * ($activeSalaryRelation->basic_percent / 100);
                $house = $emp['gross'] * ($activeSalaryRelation->house_rent_percent / 100);
                $med = $emp['gross'] * ($activeSalaryRelation->medical_percent / 100);
                $tada = $emp['gross'] * ($activeSalaryRelation->tada_percent / 100);
            }

            $profile->salaryStructure()->firstOrCreate(
                ['employee_profile_id' => $profile->id],
                [
                    'tenant_id' => $tenant->id,
                    'basic_salary' => $basic,
                    'house_rent' => $house,
                    'medical_allowance' => $med,
                    'conveyance_allowance' => $tada,
                    'other_allowances' => 0.00,
                    'provident_fund_deduction' => $emp['gross'] * 0.05,
                    'tax_deduction' => $emp['gross'] * 0.03
                ]
            );

            // Create education
            $profile->education()->firstOrCreate(
                ['degree_name' => $emp['degree']],
                [
                    'tenant_id' => $tenant->id,
                    'institution' => $emp['inst'],
                    'passing_year' => '2018',
                    'result' => 'CGPA 3.' . rand(50, 99),
                    'certification_type' => 'education'
                ]
            );

            // Create experience
            $profile->experiences()->firstOrCreate(
                ['company_name' => $emp['ex_company']],
                [
                    'tenant_id' => $tenant->id,
                    'designation' => $emp['ex_desig'],
                    'start_date' => '2022-01-01',
                    'end_date' => '2024-12-31',
                    'job_description' => 'Worked as a full-time key resource contributing to business growth and milestones.'
                ]
            );
        }
    }
}
