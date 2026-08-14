<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class GenerateTestData extends Command
{
    protected $signature = 'data:generate-test {--employees=200 : Number of employees to create} {--months=3 : Months of punch data}';
    protected $description = 'Generate comprehensive test data including employees, attendance, and payroll';

    private $tenantId = 1;
    private $deptIds;
    private $desigIds;
    private $shiftIds;
    private $leaveTypeIds;
    private $advanceTypeIds;
    private $advanceSourceIds;
    private $firstNames;
    private $lastNames;
    private $startMonth;
    private $endMonth;

    public function handle()
    {
        $count = (int) $this->option('employees');
        $months = (int) $this->option('months');

        $this->init();

        $this->info("Generating {$count} employees with {$months} months of data...");

        $this->warn('Truncating existing test data (raw_punch_data, salary_payroll, attendance_processed)...');
        DB::table('salary_payroll')->where('tenant_id', $this->tenantId)->delete();
        DB::table('attendance_processed')->where('tenant_id', $this->tenantId)->delete();
        DB::table('raw_punch_data')->where('tenant_id', $this->tenantId)->delete();

        $employees = $this->createEmployees($count);
        $this->info('✓ Employees created');

        $this->createSalaryStructures($employees);
        $this->info('✓ Salary structures created');

        $this->createPromotions($employees);
        $this->info('✓ Promotions created');

        $this->createIncrements($employees);
        $this->info('✓ Increments created');

        $this->createAdvances($employees);
        $this->info('✓ Advances created');

        $this->generatePunchData($employees);
        $this->info('✓ Raw punch data generated');

        $this->createLeaveApplications($employees);
        $this->info('✓ Leave applications created');

        $this->info("\n✅ Done! Generated {$count} employees with {$months} months of punch data.");
    }

    private function init()
    {
        $this->deptIds = DB::table('departments')->where('tenant_id', $this->tenantId)->pluck('id')->toArray();
        $this->desigIds = DB::table('designations')->where('tenant_id', $this->tenantId)->pluck('id')->toArray();
        $this->shiftIds = DB::table('work_shifts')->where('tenant_id', $this->tenantId)->pluck('id')->toArray();
        $this->leaveTypeIds = DB::table('leave_types')->pluck('id')->toArray();
        $this->advanceTypeIds = DB::table('advance_types')->pluck('id')->toArray();
        $this->advanceSourceIds = DB::table('advance_sources')->pluck('id')->toArray();

        $this->firstNames = ['Ahmad','Fatima','Mohammad','Khadija','Abdullah','Aisha','Omar','Zainab','Ali','Maryam','Hassan','Noor','Hussein','Layla','Ibrahim','Sarah','Yusuf','Hafsa','Musa','Amina','Adam','Rania','Bilal','Samira','Khalid','Yasmin','Tariq','Mariam','Saeed','Nadia','Hamza','Leila','Rashid','Ayah','Jamal','Farida','Karim','Safiya','Nasser','Iman','Fahad','Salma','Majid','Amal','Anwar','Mona','Rayan','Lina','Zayed','Huda','Tamer','Dina','Qasim','Nahid','Walid','Rasha','Zubair','Najat','Sadiq','Samar','Murtaza','Tahmina','Shahid','Nargis','Rafiq','Shirin','Mahmood','Parveen','Javed','Shabnam','Sharif','Nasreen','Rasheed','Shahnaz','Kashif','Rubina','Farhan','Sultana','Asif','Razia','Naveed','Shamim','Faisal','Rokeya','Imran','Maksuda','Tajul','Ferdous','Mizan','Jahanara','Shafiq','Shamima','Nurul','Hasina','Aziz','Rabeya','Shamim','Rowshan','Alam','Nasima','Shahjahan','Shahida','Siddique','Aklima','Wahid','Rahima','Mamun','Shamima','Rafiqul','Jahanara','Shahin','Shahnaj','Humayun','Sharmin','Ehsan','Shammee','Tahmid','Farhana','Rubel','Shammy','Nazmul','Kohinoor','Momin','Shefali','Tanvir','Rahima','Sajjad','Shammy','Mahfuz','Nipa','Shamim','Maksuda','Riaz','Shamima','Parvez','Shahida','Milon','Tahamina','Noman','Shamima','Sohag','Rowshan','Shahed','Aleya','Bashir','Shahida','Morshed','Shamim','Hasan','Shahida','Sohel','Shamima','Saiful','Shammy','Moshiur','Shamima'];

        $this->lastNames = ['Rahman','Ahmed','Khan','Islam','Hossain','Sarker','Chowdhury','Hasan','Ali','Mollah','Shah','Malik','Bhuiyan','Haque','Kabir','Siddique','Rashid','Anwar','Hossain','Karim','Jahan','Parveen','Sultana','Nasrin','Sharmin','Begum','Akhter','Khatun','Yesmin','Nargis'];

        $this->startMonth = Carbon::now()->subMonths(3)->startOfMonth();
        $this->endMonth = Carbon::now()->endOfMonth();
    }

    private function createEmployees($count)
    {
        $existingCount = DB::table('employee_profiles')->where('tenant_id', $this->tenantId)->count();
        $now = now();

        for ($i = 0; $i < $count; $i++) {
            $firstName = $this->firstNames[array_rand($this->firstNames)];
            $lastName = $this->lastNames[array_rand($this->lastNames)];
            $name = "{$firstName} {$lastName}";
            $uniqueId = $existingCount + $i + 1;
            $email = strtolower(str_replace(' ', '.', $name)) . $uniqueId . '@acme.test';
            $empId = 'EMP' . date('Y') . str_pad($uniqueId, 4, '0', STR_PAD_LEFT);
            $deptId = $this->deptIds[array_rand($this->deptIds)];
            $desigId = $this->desigIds[array_rand($this->desigIds)];
            $shiftId = $this->shiftIds[array_rand($this->shiftIds)];
            $joinDate = Carbon::now()->subMonths(rand(1, 24))->subDays(rand(0, 28))->format('Y-m-d');
            $dob = Carbon::parse($joinDate)->subYears(rand(20, 50))->subDays(rand(0, 365))->format('Y-m-d');

            $userId = DB::table('users')->insertGetId([
                'tenant_id' => $this->tenantId,
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('employee_profiles')->insert([
                'tenant_id' => $this->tenantId,
                'user_id' => $userId,
                'employee_id' => $empId,
                'joining_date' => $joinDate,
                'gender' => rand(0, 1) ? 'male' : 'female',
                'dob' => $dob,
                'phone_number' => '017' . rand(10000000, 99999999),
                'blood_group' => ['A+','B+','AB+','O+','A-','B-','O-'][array_rand(['A+','B+','O+','A-','B-','O-'])],
                'religion' => ['Islam','Hinduism','Christianity','Buddhism'][array_rand(['Islam','Hinduism','Christianity','Buddhism'])],
                'marital_status' => rand(0, 1) ? 'married' : 'unmarried',
                'status' => 'active',
                'overtime_eligible' => rand(0, 1),
                'department_id' => $deptId,
                'designation_id' => $desigId,
                'shift_id' => $shiftId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if ($i % 20 === 0) $this->output->write('.');
        }

        return DB::table('employee_profiles')
            ->where('tenant_id', $this->tenantId)
            ->where('status', 'active')
            ->orderBy('id')
            ->get()
            ->keyBy('employee_id');
    }

    private function createSalaryStructures($employees)
    {
        $existingIds = DB::table('salary_structures')
            ->where('tenant_id', $this->tenantId)
            ->pluck('employee_profile_id')
            ->toArray();

        $structures = [];
        foreach ($employees as $emp) {
            if (in_array($emp->id, $existingIds)) continue;
            $basic = rand(15000, 80000);
            $structures[] = [
                'tenant_id' => $this->tenantId,
                'employee_profile_id' => $emp->id,
                'basic_salary' => $basic,
                'house_rent' => round($basic * 0.5),
                'medical_allowance' => round($basic * 0.1),
                'conveyance_allowance' => rand(500, 3000),
                'other_allowances' => rand(0, 5000),
                'provident_fund_deduction' => round($basic * 0.05),
                'tax_deduction' => $basic > 40000 ? round($basic * 0.03) : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($structures, 50) as $chunk) {
            DB::table('salary_structures')->insert($chunk);
        }
    }

    private function createPromotions($employees)
    {
        $existingIds = DB::table('employee_promotions')
            ->where('tenant_id', $this->tenantId)
            ->pluck('employee_profile_id')
            ->toArray();

        $pool = $employees->whereNotIn('id', $existingIds);
        $sampleSize = min((int) ($employees->count() * 0.3), $pool->count());
        if ($sampleSize <= 0) return;
        $promoted = $pool->random($sampleSize);
        $records = [];

        foreach ($promoted as $emp) {
            $oldDept = $emp->department_id;
            $newDept = $this->deptIds[array_rand($this->deptIds)];
            $oldDesig = $emp->designation_id;
            $newDesig = $this->desigIds[array_rand($this->desigIds)];
            if ($newDept == $oldDept) continue;
            if ($newDesig == $oldDesig) continue;

            $records[] = [
                'tenant_id' => $this->tenantId,
                'employee_profile_id' => $emp->id,
                'old_department_id' => $oldDept,
                'new_department_id' => $newDept,
                'old_designation_id' => $oldDesig,
                'new_designation_id' => $newDesig,
                'promotion_type' => ['promotion', 'transfer', 'demotion'][array_rand(['promotion', 'transfer', 'demotion'])],
                'notes' => 'Bulk test promotion',
                'effective_date' => Carbon::parse($emp->joining_date)->addMonths(rand(3, 12))->format('Y-m-d'),
                'status' => 'active',
                'reference_number' => 'PRO-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($records, 50) as $chunk) {
            DB::table('employee_promotions')->insert($chunk);
        }
    }

    private function createIncrements($employees)
    {
        $existingIds = DB::table('increments')
            ->where('tenant_id', $this->tenantId)
            ->pluck('employee_profile_id')
            ->toArray();

        $pool = $employees->whereNotIn('id', $existingIds);
        $sampleSize = min((int) ($employees->count() * 0.4), $pool->count());
        if ($sampleSize <= 0) return;
        $incremented = $pool->random($sampleSize);
        $records = [];
        $incrementRules = DB::table('increment_rules')->where('tenant_id', $this->tenantId)->pluck('id')->toArray();
        $ruleIds = $incrementRules ?: [null];

        foreach ($incremented as $emp) {
            $structure = DB::table('salary_structures')->where('employee_profile_id', $emp->id)->first();
            if (!$structure) continue;

            $pct = rand(5, 15);
            $newBasic = round($structure->basic_salary * (1 + $pct / 100));
            $newGross = $newBasic + round($newBasic * 0.5) + round($newBasic * 0.1) + $structure->conveyance_allowance + $structure->other_allowances;
            $oldGross = $structure->basic_salary + $structure->house_rent + $structure->medical_allowance + $structure->conveyance_allowance + $structure->other_allowances;

            $records[] = [
                'tenant_id' => $this->tenantId,
                'employee_profile_id' => $emp->id,
                'increment_rule_id' => $ruleIds[array_rand($ruleIds)],
                'increment_type' => ['annual', 'special', 'manual'][array_rand(['annual', 'special', 'manual'])],
                'old_basic' => $structure->basic_salary,
                'old_gross' => $oldGross,
                'new_basic' => $newBasic,
                'new_gross' => $newGross,
                'increment_amount' => $newBasic - $structure->basic_salary,
                'increment_percentage' => $pct,
                'based_on' => 'basic',
                'status' => 'enforced',
                'enforced_at' => Carbon::parse($emp->joining_date)->addMonths(rand(6, 18))->format('Y-m-d H:i:s'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($records, 50) as $chunk) {
            DB::table('increments')->insert($chunk);
        }
    }

    private function createAdvances($employees)
    {
        $existingIds = DB::table('advances')
            ->where('tenant_id', $this->tenantId)
            ->pluck('employee_profile_id')
            ->toArray();

        $pool = $employees->whereNotIn('id', $existingIds);
        $sampleSize = min((int) ($employees->count() * 0.2), $pool->count());
        if ($sampleSize <= 0) return;
        $advanced = $pool->random($sampleSize);
        $records = [];

        foreach ($advanced as $emp) {
            $amount = rand(5000, 50000);
            $installments = rand(1, 6);
            $records[] = [
                'tenant_id' => $this->tenantId,
                'employee_profile_id' => $emp->id,
                'advance_type_id' => $this->advanceTypeIds[array_rand($this->advanceTypeIds)],
                'advance_source_id' => $this->advanceSourceIds[array_rand($this->advanceSourceIds)],
                'amount' => $amount,
                'approved_amount' => $amount,
                'installments' => $installments,
                'monthly_deduction' => round($amount / $installments),
                'reason' => 'Bulk test advance',
                'status' => 'approved',
                'actioned_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($records, 50) as $chunk) {
            DB::table('advances')->insert($chunk);
        }
    }

    private function generatePunchData($employees)
    {
        $punchRecords = [];
        $now = Carbon::now();
        $current = $this->startMonth->copy();

        $machines = ['ZK-001', 'ZK-002', 'ZK-003', 'ZK-004', 'ZK-005'];
        $verifyTypes = ['password', 'fingerprint', 'face', 'card'];
        $employeeIds = $employees->pluck('employee_id', 'id');

        $bar = $this->output->createProgressBar($employees->count() * 3);
        $bar->start();

        while ($current <= $now) {
            $daysInMonth = $current->daysInMonth;
            $month = $current->format('Y-m');

            foreach ($employees as $emp) {
                $shift = DB::table('work_shifts')->where('id', $emp->shift_id)->first();
                $startH = $shift ? (int) substr($shift->start_time, 0, 2) : 9;
                $startM = $shift ? (int) substr($shift->start_time, 3, 2) : 0;
                $endH = $shift ? (int) substr($shift->end_time, 0, 2) : 17;

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::parse("{$month}-{$day}");

                    // Skip weekends (Friday in BD)
                    if ($date->format('l') === 'Friday') continue;

                    // Random absent probability ~10%
                    if (rand(1, 100) <= 10) continue;

                    $punchDate = $date->format('Y-m-d');
                    $machine = $machines[array_rand($machines)];

                    // In-punch
                    $inMinute = rand(0, 30);
                    $isLate = rand(1, 100) <= 25;
                    if ($isLate) $inMinute = rand(35, 120); // late by up to 2hr

                    $inTime = Carbon::parse("{$punchDate} {$startH}:{$startM}:00")->addMinutes($inMinute);
                    $punchRecords[] = [
                        'tenant_id' => $this->tenantId,
                        'employee_id' => $emp->employee_id,
                        'employee_profile_id' => $emp->id,
                        'punch_machine_serial' => $machine,
                        'punch_date_time' => $inTime,
                        'status' => 'C',
                        'verify_type' => $verifyTypes[array_rand($verifyTypes)],
                        'source_file' => 'test_data',
                        'is_matched' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Out-punch (skip for some days ~5% = missing punch)
                    if (rand(1, 100) <= 5) continue;

                    $workHours = rand(6, 10);
                    $outTime = $inTime->copy()->addHours($workHours)->addMinutes(rand(-30, 60));
                    $punchRecords[] = [
                        'tenant_id' => $this->tenantId,
                        'employee_id' => $emp->employee_id,
                        'employee_profile_id' => $emp->id,
                        'punch_machine_serial' => $machine,
                        'punch_date_time' => $outTime,
                        'status' => 'C',
                        'verify_type' => $verifyTypes[array_rand($verifyTypes)],
                        'source_file' => 'test_data',
                        'is_matched' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Day switches to next
                    if (count($punchRecords) >= 500) {
                        DB::table('raw_punch_data')->insert($punchRecords);
                        $punchRecords = [];
                    }
                }
                $bar->advance();
            }
            $current->addMonth();
        }

        if (!empty($punchRecords)) {
            DB::table('raw_punch_data')->insert($punchRecords);
        }

        $bar->finish();
        $this->line('');
    }

    private function createLeaveApplications($employees)
    {
        $leaveEmployees = $employees->random((int) ($employees->count() * 0.15));
        $records = [];

        foreach ($leaveEmployees as $emp) {
            $startDate = Carbon::now()->startOfMonth()->subMonths(rand(0, 2))->addDays(rand(1, 20));
            $days = rand(1, 5);
            $endDate = $startDate->copy()->addDays($days);

            $records[] = [
                'tenant_id' => $this->tenantId,
                'employee_profile_id' => $emp->id,
                'leave_type_id' => $this->leaveTypeIds[array_rand($this->leaveTypeIds)],
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'total_days' => $days,
                'reason' => 'Bulk test leave application',
                'status' => 'approved',
                'actioned_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($records, 50) as $chunk) {
            DB::table('leave_applications')->insert($chunk);
        }
    }
}
