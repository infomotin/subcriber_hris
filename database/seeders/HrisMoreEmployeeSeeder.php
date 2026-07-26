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
use App\Models\SalaryRelation;
use App\Models\WorkShift;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class HrisMoreEmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        if (!$tenant) {
            $tenant = Tenant::create(['name' => 'Demo SaaS Organization', 'tenant_token' => 'DEMO_TOKEN_999']);
        }

        $employeeRole = Role::firstOrCreate(['name' => 'Employee']);

        // Extra departments
        $extraDepts = ['Administration', 'Sales & Marketing', 'Research & Development', 'Logistics', 'Quality Assurance'];
        $createdDepts = [];
        foreach (['Human Resources', 'Information Technology', 'Finance & Accounts', 'Operations & Support'] as $dn) {
            $createdDepts[$dn] = Department::where('tenant_id', $tenant->id)->where('name', $dn)->first();
        }
        foreach ($extraDepts as $dn) {
            $createdDepts[$dn] = Department::firstOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $dn],
                ['code' => strtoupper(substr($dn, 0, 3)) . rand(10, 99)]
            );
        }

        // Extra designations
        $extraDesigs = [
            'Administrative Officer' => 'G3',
            'Software Engineer' => 'G3',
            'Junior Software Engineer' => 'G4',
            'Marketing Manager' => 'G2',
            'Sales Executive' => 'G4',
            'R&D Engineer' => 'G3',
            'Logistics Coordinator' => 'G4',
            'QA Engineer' => 'G3',
            'Accountant' => 'G4',
            'Office Assistant' => 'G5',
            'Production Worker' => 'G5',
            'System Administrator' => 'G3',
            'UI/UX Designer' => 'G3',
            'Business Analyst' => 'G3',
            'Team Lead' => 'G2',
        ];
        $createdDesigs = [];
        foreach (['HR Manager', 'Senior Software Engineer', 'Chief Technology Officer', 'Senior Accountant', 'Support Executive'] as $dt) {
            $createdDesigs[$dt] = Designation::where('tenant_id', $tenant->id)->where('title', $dt)->first();
        }
        foreach ($extraDesigs as $title => $grade) {
            $createdDesigs[$title] = Designation::firstOrCreate(
                ['tenant_id' => $tenant->id, 'title' => $title],
                ['grade' => $grade]
            );
        }

        // Shift data
        $morningShift = WorkShift::where('tenant_id', $tenant->id)->where('name', 'Regular Morning Shift')->first();
        $eveningShift = WorkShift::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Evening Shift'],
            ['start_time' => '14:00:00', 'end_time' => '22:00:00', 'late_buffer_time' => '14:15:00']
        );
        $nightShift = WorkShift::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Night Shift'],
            ['start_time' => '22:00:00', 'end_time' => '06:00:00', 'late_buffer_time' => '22:15:00']
        );
        $shifts = [1 => $morningShift, 2 => $eveningShift, 3 => $nightShift];

        $maleGender = Gender::where('tenant_id', $tenant->id)->where('name', 'Male')->first();
        $femaleGender = Gender::where('tenant_id', $tenant->id)->where('name', 'Female')->first();
        $dhakaDiv = Division::where('name', 'Dhaka')->first();
        $dhakaDist = District::where('division_id', $dhakaDiv?->id)->where('name', 'Dhaka')->first();
        $mirpurThana = Thana::where('district_id', $dhakaDist?->id)->where('name', 'Mirpur')->first();
        $uttaraThana = Thana::where('district_id', $dhakaDist?->id)->where('name', 'Uttara')->first();
        $activeSalaryRelation = SalaryRelation::where('tenant_id', $tenant->id)->where('is_active', true)->first();

        $startId = EmployeeProfile::max('id') ?? 0;

        $employeeData = [
            ['name' => 'Rafiq Hasan', 'email' => 'rafiq@amds.test', 'emp_id' => 'EMP2026006', 'title' => 'Administrative Officer', 'dept' => 'Administration', 'gender' => 'Male', 'dob' => '1990-03-10', 'phone' => '+8801711223346', 'blood' => 'A+', 'gross' => 45000, 'degree' => 'MSS in Public Admin', 'inst' => 'Dhaka University', 'ex_company' => 'Government Office', 'ex_desig' => 'Junior Officer', 'type' => 'staff', 'overtime' => false, 'rate' => null, 'shift' => 1, 'joining' => '2023-06-01'],
            ['name' => 'Shamim Reza', 'email' => 'shamim@amds.test', 'emp_id' => 'EMP2026007', 'title' => 'Software Engineer', 'dept' => 'Information Technology', 'gender' => 'Male', 'dob' => '1995-07-22', 'phone' => '+8801711223347', 'blood' => 'B+', 'gross' => 65000, 'degree' => 'B.Sc. in CSE', 'inst' => 'BUET', 'ex_company' => 'Kaz Software', 'ex_desig' => 'Jr. Software Engineer', 'type' => 'staff', 'overtime' => false, 'rate' => null, 'shift' => 1, 'joining' => '2024-01-15'],
            ['name' => 'Nasrin Akter', 'email' => 'nasrin@amds.test', 'emp_id' => 'EMP2026008', 'title' => 'Marketing Manager', 'dept' => 'Sales & Marketing', 'gender' => 'Female', 'dob' => '1992-11-05', 'phone' => '+8801711223348', 'blood' => 'O+', 'gross' => 70000, 'degree' => 'MBA in Marketing', 'inst' => 'North South University', 'ex_company' => 'Square Group', 'ex_desig' => 'Marketing Executive', 'type' => 'manager', 'overtime' => false, 'rate' => null, 'shift' => 1, 'joining' => '2022-09-01'],
            ['name' => 'Kamal Hossain', 'email' => 'kamal@amds.test', 'emp_id' => 'EMP2026009', 'title' => 'Production Worker', 'dept' => 'Operations & Support', 'gender' => 'Male', 'dob' => '1998-01-15', 'phone' => '+8801711223349', 'blood' => 'AB+', 'gross' => 18000, 'degree' => 'HSC', 'inst' => 'Dhaka College', 'ex_company' => 'Garments Factory', 'ex_desig' => 'Operator', 'type' => 'worker', 'overtime' => true, 'rate' => 180, 'shift' => 2, 'joining' => '2025-03-01'],
            ['name' => 'Jahanara Begum', 'email' => 'jahanara@amds.test', 'emp_id' => 'EMP2026010', 'title' => 'QA Engineer', 'dept' => 'Quality Assurance', 'gender' => 'Female', 'dob' => '1993-09-18', 'phone' => '+8801711223350', 'blood' => 'A-', 'gross' => 55000, 'degree' => 'B.Sc. in EEE', 'inst' => 'KUET', 'ex_company' => 'SoftTech Ltd', 'ex_desig' => 'Manual Tester', 'type' => 'staff', 'overtime' => false, 'rate' => null, 'shift' => 1, 'joining' => '2024-06-01'],
            ['name' => 'Mehedi Hasan', 'email' => 'mehedi@amds.test', 'emp_id' => 'EMP2026011', 'title' => 'Junior Software Engineer', 'dept' => 'Information Technology', 'gender' => 'Male', 'dob' => '1999-04-30', 'phone' => '+8801711223351', 'blood' => 'B-', 'gross' => 35000, 'degree' => 'B.Sc. in CSE', 'inst' => 'AIUB', 'ex_company' => null, 'ex_desig' => null, 'type' => 'staff', 'overtime' => false, 'rate' => null, 'shift' => 1, 'joining' => '2026-01-01'],
            ['name' => 'Sharmin Sultana', 'email' => 'sharmin@amds.test', 'emp_id' => 'EMP2026012', 'title' => 'Accountant', 'dept' => 'Finance & Accounts', 'gender' => 'Female', 'dob' => '1994-12-12', 'phone' => '+8801711223352', 'blood' => 'O-', 'gross' => 38000, 'degree' => 'BBA in Accounting', 'inst' => 'Jahangirnagar University', 'ex_company' => 'NGO Foundation', 'ex_desig' => 'Accounts Assistant', 'type' => 'staff', 'overtime' => false, 'rate' => null, 'shift' => 1, 'joining' => '2023-11-01'],
            ['name' => 'Abdur Rahim', 'email' => 'rahim@amds.test', 'emp_id' => 'EMP2026013', 'title' => 'Logistics Coordinator', 'dept' => 'Logistics', 'gender' => 'Male', 'dob' => '1991-06-25', 'phone' => '+8801711223353', 'blood' => 'A+', 'gross' => 42000, 'degree' => 'BBA in Supply Chain', 'inst' => 'IBA, Dhaka University', 'ex_company' => 'Pran Group', 'ex_desig' => 'Logistics Officer', 'type' => 'staff', 'overtime' => true, 'rate' => 200, 'shift' => 1, 'joining' => '2022-04-01'],
            ['name' => 'Selina Parvin', 'email' => 'selina@amds.test', 'emp_id' => 'EMP2026014', 'title' => 'Office Assistant', 'dept' => 'Administration', 'gender' => 'Female', 'dob' => '2000-08-14', 'phone' => '+8801711223354', 'blood' => 'B+', 'gross' => 15000, 'degree' => 'SSC', 'inst' => 'Mirpur High School', 'ex_company' => null, 'ex_desig' => null, 'type' => 'worker', 'overtime' => true, 'rate' => 150, 'shift' => 1, 'joining' => '2025-07-01'],
            ['name' => 'Shahidul Islam', 'email' => 'shahidul@amds.test', 'emp_id' => 'EMP2026015', 'title' => 'R&D Engineer', 'dept' => 'Research & Development', 'gender' => 'Male', 'dob' => '1989-05-20', 'phone' => '+8801711223355', 'blood' => 'AB-', 'gross' => 85000, 'degree' => 'M.Sc. in ME', 'inst' => 'KUET', 'ex_company' => 'EnergyPac', 'ex_desig' => 'Design Engineer', 'type' => 'manager', 'overtime' => false, 'rate' => null, 'shift' => 1, 'joining' => '2021-03-01'],
            ['name' => 'Tahmina Akhter', 'email' => 'tahmina@amds.test', 'emp_id' => 'EMP2026016', 'title' => 'Sales Executive', 'dept' => 'Sales & Marketing', 'gender' => 'Female', 'dob' => '1996-10-08', 'phone' => '+8801711223356', 'blood' => 'O+', 'gross' => 28000, 'degree' => 'BBA in Marketing', 'inst' => 'Eastern University', 'ex_company' => 'Banglalink', 'ex_desig' => 'Sales Associate', 'type' => 'staff', 'overtime' => false, 'rate' => null, 'shift' => 1, 'joining' => '2024-09-01'],
            ['name' => 'Firoz Alam', 'email' => 'firoz@amds.test', 'emp_id' => 'EMP2026017', 'title' => 'System Administrator', 'dept' => 'Information Technology', 'gender' => 'Male', 'dob' => '1993-02-28', 'phone' => '+8801711223357', 'blood' => 'A+', 'gross' => 60000, 'degree' => 'B.Sc. in IT', 'inst' => 'IIT, Dhaka University', 'ex_company' => 'ServerHub BD', 'ex_desig' => 'Network Engineer', 'type' => 'staff', 'overtime' => true, 'rate' => 250, 'shift' => 3, 'joining' => '2023-08-01'],
            ['name' => 'Rokeya Khatun', 'email' => 'rokeya@amds.test', 'emp_id' => 'EMP2026018', 'title' => 'Production Worker', 'dept' => 'Operations & Support', 'gender' => 'Female', 'dob' => '1997-12-03', 'phone' => '+8801711223358', 'blood' => 'B+', 'gross' => 16000, 'degree' => 'SSC', 'inst' => 'Demra High School', 'ex_company' => null, 'ex_desig' => null, 'type' => 'worker', 'overtime' => true, 'rate' => 160, 'shift' => 2, 'joining' => '2025-06-15'],
            ['name' => 'Ashikur Rahman', 'email' => 'ashik@amds.test', 'emp_id' => 'EMP2026019', 'title' => 'UI/UX Designer', 'dept' => 'Information Technology', 'gender' => 'Male', 'dob' => '1995-09-14', 'phone' => '+8801711223359', 'blood' => 'AB+', 'gross' => 52000, 'degree' => 'B.Sc. in CS', 'inst' => 'Daffodil International University', 'ex_company' => 'ThemeForest', 'ex_desig' => 'Graphic Designer', 'type' => 'staff', 'overtime' => false, 'rate' => null, 'shift' => 1, 'joining' => '2024-04-01'],
            ['name' => 'Morshed Alam', 'email' => 'morshed@amds.test', 'emp_id' => 'EMP2026020', 'title' => 'Business Analyst', 'dept' => 'Information Technology', 'gender' => 'Male', 'dob' => '1992-07-11', 'phone' => '+8801711223360', 'blood' => 'O+', 'gross' => 58000, 'degree' => 'MBA in MIS', 'inst' => 'IBA, Dhaka University', 'ex_company' => 'Reve Systems', 'ex_desig' => 'System Analyst', 'type' => 'staff', 'overtime' => false, 'rate' => null, 'shift' => 1, 'joining' => '2022-11-01'],
            ['name' => 'Nazma Begum', 'email' => 'nazma@amds.test', 'emp_id' => 'EMP2026021', 'title' => 'Production Worker', 'dept' => 'Operations & Support', 'gender' => 'Female', 'dob' => '1999-03-22', 'phone' => '+8801711223361', 'blood' => 'A-', 'gross' => 14000, 'degree' => 'Class 8', 'inst' => null, 'ex_company' => null, 'ex_desig' => null, 'type' => 'worker', 'overtime' => true, 'rate' => 150, 'shift' => 2, 'joining' => '2026-02-01'],
            ['name' => 'Farhad Hossain', 'email' => 'farhad@amds.test', 'emp_id' => 'EMP2026022', 'title' => 'Team Lead', 'dept' => 'Information Technology', 'gender' => 'Male', 'dob' => '1990-11-30', 'phone' => '+8801711223362', 'blood' => 'B+', 'gross' => 95000, 'degree' => 'B.Sc. in CSE', 'inst' => 'BUET', 'ex_company' => 'Samsung R&D', 'ex_desig' => 'Senior Engineer', 'type' => 'manager', 'overtime' => false, 'rate' => null, 'shift' => 1, 'joining' => '2020-07-01'],
            ['name' => 'Shamima Yasmin', 'email' => 'shamima@amds.test', 'emp_id' => 'EMP2026023', 'title' => 'HR Manager', 'dept' => 'Human Resources', 'gender' => 'Female', 'dob' => '1991-04-18', 'phone' => '+8801711223363', 'blood' => 'O+', 'gross' => 65000, 'degree' => 'MBA in HRM', 'inst' => 'University of Dhaka', 'ex_company' => 'BRAC Bank', 'ex_desig' => 'HR Officer', 'type' => 'manager', 'overtime' => false, 'rate' => null, 'shift' => 1, 'joining' => '2021-01-01'],
            ['name' => 'Joynal Abedin', 'email' => 'joynal@amds.test', 'emp_id' => 'EMP2026024', 'title' => 'Support Executive', 'dept' => 'Operations & Support', 'gender' => 'Male', 'dob' => '1997-08-05', 'phone' => '+8801711223364', 'blood' => 'B-', 'gross' => 25000, 'degree' => 'B.A.', 'inst' => 'National University', 'ex_company' => 'GP Customer Care', 'ex_desig' => 'Call Center Agent', 'type' => 'staff', 'overtime' => true, 'rate' => 190, 'shift' => 2, 'joining' => '2025-01-01'],
            ['name' => 'Hasina Begum', 'email' => 'hasina@amds.test', 'emp_id' => 'EMP2026025', 'title' => 'Accountant', 'dept' => 'Finance & Accounts', 'gender' => 'Female', 'dob' => '1996-06-27', 'phone' => '+8801711223365', 'blood' => 'A+', 'gross' => 35000, 'degree' => 'BBA in Finance', 'inst' => 'Stamford University', 'ex_company' => 'MicroCredit Org', 'ex_desig' => 'Accounts Assistant', 'type' => 'staff', 'overtime' => false, 'rate' => null, 'shift' => 1, 'joining' => '2024-07-01'],
        ];

        foreach ($employeeData as $emp) {
            $user = User::firstOrCreate(
                ['email' => $emp['email']],
                [
                    'tenant_id' => $tenant->id,
                    'name' => $emp['name'],
                    'password' => Hash::make('password')
                ]
            );
            $user->syncRoles([$employeeRole]);

            $dept = $createdDepts[$emp['dept']] ?? null;
            $desig = $createdDesigs[$emp['title']] ?? null;
            $shift = $shifts[$emp['shift']] ?? $morningShift;

            $profile = EmployeeProfile::firstOrCreate(
                ['employee_id' => $emp['emp_id']],
                [
                    'tenant_id' => $tenant->id,
                    'user_id' => $user->id,
                    'department_id' => $dept?->id,
                    'designation_id' => $desig?->id,
                    'shift_id' => $shift?->id,
                    'employee_type' => $emp['type'],
                    'overtime_eligible' => $emp['overtime'],
                    'overtime_rate' => $emp['rate'],
                    'joining_date' => $emp['joining'],
                    'gender' => $emp['gender'],
                    'dob' => $emp['dob'],
                    'phone_number' => $emp['phone'],
                    'blood_group' => $emp['blood'],
                    'status' => 'active',
                ]
            );

            $profile->addresses()->firstOrCreate(
                ['type' => 'current'],
                [
                    'tenant_id' => $tenant->id,
                    'address_line_1' => 'House ' . rand(1, 500) . ', Road ' . rand(1, 30),
                    'city' => ($profile->id % 2 == 0) ? 'Uttara, Dhaka' : 'Mirpur, Dhaka',
                    'state' => 'Dhaka',
                    'zip_code' => '12' . rand(10, 99),
                    'country' => 'Bangladesh',
                    'is_active' => true,
                ]
            );

            $profile->bankInfo()->firstOrCreate(
                ['employee_profile_id' => $profile->id],
                [
                    'tenant_id' => $tenant->id,
                    'bank_name' => collect(['Dutch-Bangla Bank', 'BRAC Bank', 'City Bank', 'Islami Bank', 'Sonali Bank'])->random(),
                    'branch_name' => 'Main Branch',
                    'account_name' => $emp['name'],
                    'account_number' => '10' . rand(100000, 999999) . rand(1000, 9999),
                    'routing_number' => '090' . rand(100000, 999999),
                    'payment_mode' => 'bank_transfer',
                ]
            );

            $gross = $emp['gross'];
            $basic = $gross * 0.50;
            $house = $gross * 0.25;
            $med = $gross * 0.10;
            $tada = $gross * 0.15;

            if ($activeSalaryRelation) {
                $basic = $gross * ($activeSalaryRelation->basic_percent / 100);
                $house = $gross * ($activeSalaryRelation->house_rent_percent / 100);
                $med = $gross * ($activeSalaryRelation->medical_percent / 100);
                $tada = $gross * ($activeSalaryRelation->tada_percent / 100);
            }

            $profile->salaryStructure()->firstOrCreate(
                ['employee_profile_id' => $profile->id],
                [
                    'tenant_id' => $tenant->id,
                    'basic_salary' => round($basic, 2),
                    'house_rent' => round($house, 2),
                    'medical_allowance' => round($med, 2),
                    'conveyance_allowance' => round($tada, 2),
                    'other_allowances' => 0.00,
                    'provident_fund_deduction' => round($gross * 0.05, 2),
                    'tax_deduction' => round($gross * 0.03, 2),
                ]
            );

            if ($emp['degree'] && $emp['inst']) {
                $profile->education()->firstOrCreate(
                    ['degree_name' => $emp['degree']],
                    [
                        'tenant_id' => $tenant->id,
                        'institution' => $emp['inst'],
                        'passing_year' => (string) rand(2015, 2023),
                        'result' => 'CGPA 3.' . rand(0, 99),
                        'certification_type' => 'education',
                    ]
                );
            }

            if ($emp['ex_company']) {
                $profile->experiences()->firstOrCreate(
                    ['company_name' => $emp['ex_company']],
                    [
                        'tenant_id' => $tenant->id,
                        'designation' => $emp['ex_desig'] ?? 'N/A',
                        'start_date' => '2020-01-01',
                        'end_date' => '2024-12-31',
                        'job_description' => 'Worked as a full-time employee contributing to organizational objectives.',
                    ]
                );
            }
        }

        $this->command->info('Seeded ' . count($employeeData) . ' additional employees.');
    }
}
