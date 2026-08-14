<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\EmployeeProfile;
use App\Models\SalaryStructure;
use App\Models\SalaryRelation;
use App\Models\Increment;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    protected function getTenantId()
    {
        return auth()->user()->tenant->id ?? Tenant::first()->id;
    }

    public function salaryRole()
    {
        $tenantId = $this->getTenantId();
        $relations = SalaryRelation::where('tenant_id', $tenantId)->with('assignments.department', 'bonusConfig.slabs')->orderBy('id')->get();
        $activeRelation = SalaryRelation::where('tenant_id', $tenantId)->where('is_active', true)->first();
        $departments = Department::where('tenant_id', $tenantId)->orderBy('name')->get();
        $months = DB::table('salary_role_assignments')
            ->where('tenant_id', $tenantId)
            ->select('applicable_month')
            ->distinct()
            ->orderBy('applicable_month', 'desc')
            ->pluck('applicable_month');

        return view('subscriber.payroll.salary-role', compact('relations', 'activeRelation', 'departments', 'months'));
    }

    public function storeSalaryRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'basic_percent' => 'required|numeric|min:0|max:100',
            'house_rent_percent' => 'required|numeric|min:0|max:100',
            'medical_percent' => 'required|numeric|min:0|max:100',
            'tada_percent' => 'required|numeric|min:0|max:100',
            'is_ot_payable' => 'boolean',
            'is_late_deduction' => 'boolean',
            'single_punch_full_day' => 'boolean',
        ]);

        $total = $request->basic_percent + $request->house_rent_percent + $request->medical_percent + $request->tada_percent;
        if (abs($total - 100) > 0.01) {
            return back()->withErrors(['total' => "Percentages must sum to 100%. Current total: {$total}%"])->withInput();
        }

        SalaryRelation::create([
            'tenant_id' => $this->getTenantId(),
            'name' => $request->name,
            'basic_percent' => $request->basic_percent,
            'house_rent_percent' => $request->house_rent_percent,
            'medical_percent' => $request->medical_percent,
            'tada_percent' => $request->tada_percent,
            'is_active' => false,
            'is_ot_payable' => $request->boolean('is_ot_payable'),
            'is_late_deduction' => $request->boolean('is_late_deduction'),
            'single_punch_full_day' => $request->boolean('single_punch_full_day'),
        ]);

        return redirect()->route('subscriber.payroll.salary-role')
            ->with('success', 'Salary role created successfully.');
    }

    public function updateSalaryRole(Request $request, $id)
    {
        $tenantId = $this->getTenantId();
        $role = SalaryRelation::where('tenant_id', $tenantId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'basic_percent' => 'required|numeric|min:0|max:100',
            'house_rent_percent' => 'required|numeric|min:0|max:100',
            'medical_percent' => 'required|numeric|min:0|max:100',
            'tada_percent' => 'required|numeric|min:0|max:100',
            'is_ot_payable' => 'boolean',
            'is_late_deduction' => 'boolean',
            'single_punch_full_day' => 'boolean',
        ]);

        $total = $request->basic_percent + $request->house_rent_percent + $request->medical_percent + $request->tada_percent;
        if (abs($total - 100) > 0.01) {
            return back()->withErrors(['total' => "Percentages must sum to 100%. Current total: {$total}%"])->withInput();
        }

        $role->update([
            'name' => $request->name,
            'basic_percent' => $request->basic_percent,
            'house_rent_percent' => $request->house_rent_percent,
            'medical_percent' => $request->medical_percent,
            'tada_percent' => $request->tada_percent,
            'is_ot_payable' => $request->boolean('is_ot_payable'),
            'is_late_deduction' => $request->boolean('is_late_deduction'),
            'single_punch_full_day' => $request->boolean('single_punch_full_day'),
        ]);

        return redirect()->route('subscriber.payroll.salary-role')
            ->with('success', 'Salary role updated successfully.');
    }

    public function activateSalaryRole($id)
    {
        $tenantId = $this->getTenantId();
        SalaryRelation::where('tenant_id', $tenantId)->update(['is_active' => false]);
        SalaryRelation::where('tenant_id', $tenantId)->where('id', $id)->update(['is_active' => true]);

        return redirect()->route('subscriber.payroll.salary-role')
            ->with('success', 'Salary role activated successfully.');
    }

    public function deleteSalaryRole($id)
    {
        SalaryRelation::where('tenant_id', $this->getTenantId())->where('id', $id)->delete();

        return redirect()->route('subscriber.payroll.salary-role')
            ->with('success', 'Salary role deleted successfully.');
    }

    public function storeBonusConfig(Request $request)
    {
        $tenantId = $this->getTenantId();
        $request->validate([
            'salary_role_id' => 'required|exists:salary_relations,id',
            'calculation_type' => 'required|in:basic_half,gross_1_5x,basic_percent,gross_percent,fixed_amount',
            'calculation_value' => 'required|numeric|min:0',
            'slabs' => 'required|array|min:1',
            'slabs.*.min_months' => 'required|integer|min:0',
            'slabs.*.max_months' => 'nullable|integer|gte:slabs.*.min_months',
            'slabs.*.percent_of_bonus' => 'required|numeric|min:0|max:100',
        ]);

        SalaryRelation::where('tenant_id', $tenantId)->findOrFail($request->salary_role_id);

        $config = \App\Models\BonusConfig::updateOrCreate(
            ['tenant_id' => $tenantId, 'salary_role_id' => $request->salary_role_id],
            [
                'calculation_type' => $request->calculation_type,
                'calculation_value' => $request->calculation_value,
            ]
        );

        $config->slabs()->delete();
        foreach ($request->slabs as $slab) {
            $config->slabs()->create([
                'min_months' => $slab['min_months'],
                'max_months' => $slab['max_months'] ?: null,
                'percent_of_bonus' => $slab['percent_of_bonus'],
            ]);
        }

        return redirect()->route('subscriber.payroll.salary-role')
            ->with('success', 'Bonus configuration saved successfully.');
    }

    public function deleteBonusConfig($id)
    {
        $tenantId = $this->getTenantId();
        $config = \App\Models\BonusConfig::where('tenant_id', $tenantId)->findOrFail($id);
        $config->delete();

        return redirect()->route('subscriber.payroll.salary-role')
            ->with('success', 'Bonus configuration removed.');
    }

    public function storeRoleAssignment(Request $request)
    {
        $tenantId = $this->getTenantId();
        $request->validate([
            'salary_role_id' => 'required|exists:salary_relations,id',
            'applicable_month' => 'required|date_format:Y-m',
            'department_ids' => 'nullable|array',
            'department_ids.*' => 'exists:departments,id',
        ]);

        $role = SalaryRelation::where('tenant_id', $tenantId)->findOrFail($request->salary_role_id);

        $departmentIds = $request->department_ids ?? [null];

        if (empty($departmentIds)) {
            $departmentIds = [null];
        }

        $created = 0;
        foreach ($departmentIds as $deptId) {
            $exists = SalaryRoleAssignment::where('tenant_id', $tenantId)
                ->where('salary_role_id', $role->id)
                ->where('department_id', $deptId)
                ->where('applicable_month', $request->applicable_month)
                ->exists();

            if (!$exists) {
                SalaryRoleAssignment::create([
                    'tenant_id' => $tenantId,
                    'salary_role_id' => $role->id,
                    'department_id' => $deptId,
                    'applicable_month' => $request->applicable_month,
                ]);
                $created++;
            }
        }

        return redirect()->route('subscriber.payroll.salary-role')
            ->with('success', "Role assigned to {$created} department(s) for {$request->applicable_month}.");
    }

    public function deleteRoleAssignment($id)
    {
        $tenantId = $this->getTenantId();
        SalaryRoleAssignment::where('tenant_id', $tenantId)->where('id', $id)->delete();

        return redirect()->route('subscriber.payroll.salary-role')
            ->with('success', 'Role assignment removed.');
    }

    public function database()
    {
        $tenantId = $this->getTenantId();
        $employees = EmployeeProfile::where('tenant_id', $tenantId)
            ->with(['salaryStructure', 'department', 'designation', 'user'])
            ->paginate(20);

        $stats = [
            'total' => EmployeeProfile::where('tenant_id', $tenantId)->count(),
            'withSalary' => SalaryStructure::where('tenant_id', $tenantId)->count(),
            'withoutSalary' => EmployeeProfile::where('tenant_id', $tenantId)
                ->whereDoesntHave('salaryStructure')->count(),
            'totalPayroll' => SalaryStructure::where('tenant_id', $tenantId)
                ->sum(DB::raw('basic_salary + house_rent + medical_allowance + conveyance_allowance + other_allowances')),
        ];

        return view('subscriber.payroll.database', compact('employees', 'stats'));
    }

    public function salaryGenerate()
    {
        $tenantId = $this->getTenantId();
        $departments = Department::where('tenant_id', $tenantId)->orderBy('name')->get();
        $designations = Designation::where('tenant_id', $tenantId)->orderBy('title')->get();
        $roles = SalaryRelation::where('tenant_id', $tenantId)->get();
        $month = request('month', date('Y-m'));

        $query = EmployeeProfile::where('tenant_id', $tenantId)->with('salaryStructure', 'department', 'user');

        if (request('department_id')) {
            $query->where('department_id', request('department_id'));
        }
        if (request('designation_id')) {
            $query->where('designation_id', request('designation_id'));
        }

        $employees = $query->get();

        $payrollData = [];
        foreach ($employees as $emp) {
            $payrollData[$emp->id] = $this->calculatePayroll($emp, $month, $tenantId);
        }

        $existingPayroll = DB::table('salary_payroll')
            ->where('tenant_id', $tenantId)
            ->where('year_month', $month)
            ->pluck('status', 'employee_profile_id')
            ->toArray();

        $payrollStats = DB::table('salary_payroll')
            ->where('tenant_id', $tenantId)
            ->where('year_month', $month)
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('ROUND(SUM(net_payable), 2) as total_net'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $monthsWithData = DB::table('attendance_processed')
            ->where('tenant_id', $tenantId)
            ->select(DB::raw("DATE_FORMAT(date, '%Y-%m') as ym"))
            ->distinct()
            ->orderBy('ym', 'desc')
            ->pluck('ym');

        return view('subscriber.payroll.salary-generate', compact(
            'departments', 'designations', 'roles', 'employees', 'month', 'payrollData', 'existingPayroll', 'monthsWithData', 'payrollStats'
        ));
    }

    public function generateSalary(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employee_profiles,id',
        ]);

        $month = $request->month;
        $employeeIds = $request->employee_ids;
        $tenantId = $this->getTenantId();
        $count = 0;

        $employees = EmployeeProfile::whereIn('id', $employeeIds)
            ->where('tenant_id', $tenantId)
            ->with('salaryStructure', 'department')
            ->get();

        DB::table('salary_payroll')
            ->where('tenant_id', $tenantId)
            ->where('year_month', $month)
            ->whereIn('employee_profile_id', $employeeIds)
            ->delete();

        foreach ($employees as $employee) {
            if (!$employee->salaryStructure) continue;

            $calc = $this->calculatePayroll($employee, $month, $tenantId);
            if (!$calc) continue;

            DB::table('salary_payroll')->insert([
                'tenant_id' => $tenantId,
                'employee_profile_id' => $employee->id,
                'year_month' => $month,
                'salary_role_id' => $calc['salary_role_id'],
                'gross_salary' => $calc['gross_salary'],
                'basic_salary' => $calc['basic_salary'],
                'house_rent' => $calc['house_rent'],
                'medical' => $calc['medical'],
                'conveyance' => $calc['conveyance'],
                'other_allowances' => $calc['other_allowances'],
                'total_days' => $calc['total_days'],
                'present_days' => $calc['present_days'],
                'absent_days' => $calc['absent_days'],
                'leave_days' => $calc['leave_days'],
                'holiday_days' => $calc['holiday_days'],
                'weekend_days' => $calc['weekend_days'],
                'working_days' => $calc['working_days'],
                'total_late_minutes' => $calc['total_late_minutes'],
                'total_ot_minutes' => $calc['total_ot_minutes'],
                'total_early_minutes' => $calc['total_early_minutes'],
                'daily_rate' => $calc['daily_rate'],
                'per_minute_rate' => $calc['per_minute_rate'],
                'late_deduction' => $calc['late_deduction'],
                'absent_deduction' => $calc['absent_deduction'],
                'ot_payable' => $calc['ot_payable'],
                'bonus_amount' => $calc['bonus_amount'],
                'bonus_eligible_percent' => $calc['bonus_eligible_percent'],
                'tenure_months' => $calc['tenure_months'],
                'advance_deduction' => $calc['advance_deduction'],
                'tax_deduction' => $calc['tax_deduction'],
                'pf_deduction' => $calc['pf_deduction'],
                'net_payable' => $calc['net_payable'],
                'status' => 'generated',
                'generated_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $count++;
        }

        return redirect()->route('subscriber.payroll.salary-generate', ['month' => $month])
            ->with('success', "Salary generated for {$count} employee(s) for {$month}");
    }

    public function deleteSalaryPayroll(Request $request)
    {
        $request->validate(['month' => 'required|date_format:Y-m']);
        $tenantId = $this->getTenantId();
        $month = $request->month;

        $count = DB::table('salary_payroll')
            ->where('tenant_id', $tenantId)
            ->where('year_month', $month)
            ->where('status', 'generated')
            ->delete();

        return redirect()->route('subscriber.payroll.salary-generate', ['month' => $month])
            ->with('success', "Removed {$count} draft payroll record(s) for {$month}. Confirmed records were kept.");
    }

    public function confirmSalaryPayroll(Request $request)
    {
        $request->validate(['month' => 'required|date_format:Y-m']);
        $tenantId = $this->getTenantId();
        $month = $request->month;

        $count = DB::table('salary_payroll')
            ->where('tenant_id', $tenantId)
            ->where('year_month', $month)
            ->where('status', 'generated')
            ->update([
                'status' => 'approved',
                'updated_at' => now(),
            ]);

        return redirect()->route('subscriber.payroll.salary-generate', ['month' => $month])
            ->with('success', "{$count} payroll record(s) confirmed for {$month}. Salary is now locked and ready for payment.");
    }

    private function calculatePayroll($employee, $month, $tenantId)
    {
        $structure = $employee->salaryStructure;
        if (!$structure) return null;

        $grossSalary = $structure->basic_salary + $structure->house_rent + $structure->medical_allowance
            + $structure->conveyance_allowance + $structure->other_allowances;

        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);

        $attendance = DB::table('attendance_processed')
            ->where('tenant_id', $tenantId)
            ->where('employee_profile_id', $employee->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->get();

        $totalDays = $attendance->count();
        $presentDays = $attendance->where('status', 'present')->count();
        $absentDays = $attendance->where('status', 'absent')->count();
        $leaveDays = $attendance->where('status', 'leave')->count();
        $holidayDays = $attendance->where('status', 'holiday')->count();
        $weekendDays = $attendance->where('status', 'weekend')->count();
        $workingDays = $presentDays + $leaveDays;

        $totalLateMinutes = (int) $attendance->sum('late_minutes');
        $totalOtMinutes = (int) $attendance->sum('overtime_minutes');
        $totalEarlyMinutes = (int) $attendance->sum('early_minutes');

        $calendarDays = (int) date('t', strtotime("{$year}-{$monthNum}-01"));
        $dailyRate = $workingDays > 0 ? round($grossSalary / $calendarDays, 2) : 0;
        $perMinuteRate = 0;

        $shift = DB::table('work_shifts')
            ->where('id', $employee->shift_id)
            ->first();
        $dailyHours = $shift ? $this->shiftDailyHours($shift) : 8;
        $perMinuteRate = $dailyRate > 0 && $dailyHours > 0
            ? round($dailyRate / ($dailyHours * 60), 4)
            : 0;

        $role = $this->resolveSalaryRole($employee, $month, $tenantId);

        $lateDeduction = 0;
        if ($role && $role->is_late_deduction && $totalLateMinutes > 0) {
            $lateDeduction = round($totalLateMinutes * $perMinuteRate, 2);
        }

        $absentDeduction = round($absentDays * $dailyRate, 2);

        $otPayable = 0;
        if ($role && $role->is_ot_payable && $totalOtMinutes > 0) {
            $otRate = $perMinuteRate * 1.5;
            $otPayable = round($totalOtMinutes * $otRate, 2);
        }

        $advanceDeduction = (float) DB::table('advances')
            ->where('tenant_id', $tenantId)
            ->where('employee_profile_id', $employee->id)
            ->where('status', 'approved')
            ->where('monthly_deduction', '>', 0)
            ->sum('monthly_deduction');

        $taxDeduction = (float) ($structure->tax_deduction ?? 0);
        $pfDeduction = (float) ($structure->provident_fund_deduction ?? 0);

        $bonusAmount = 0;
        $bonusEligiblePercent = 0;
        $tenureMonths = 0;

        if ($role) {
            $bonusConfig = \App\Models\BonusConfig::where('salary_role_id', $role->id)->with('slabs')->first();
            if ($bonusConfig) {
                $joiningDate = $employee->joining_date;
                if ($joiningDate) {
                    $bonusDate = \Carbon\Carbon::parse("{$year}-{$monthNum}-01")->endOfMonth();
                    $join = \Carbon\Carbon::parse($joiningDate);
                    $tenureMonths = max(0, (int) $join->diffInMonths($bonusDate));
                    $bonusEligiblePercent = $bonusConfig->getEligibilityPercent($tenureMonths);
                    if ($bonusEligiblePercent > 0) {
                        $fullBonus = $bonusConfig->calculateBonusAmount($structure->basic_salary, $grossSalary);
                        $bonusAmount = round($fullBonus * ($bonusEligiblePercent / 100), 2);
                    }
                }
            }
        }

        $netPayable = max(0, $grossSalary - $lateDeduction - $absentDeduction
            + $otPayable + $bonusAmount - $advanceDeduction - $taxDeduction - $pfDeduction);

        return [
            'salary_role_id' => $role?->id,
            'gross_salary' => $grossSalary,
            'basic_salary' => $structure->basic_salary,
            'house_rent' => $structure->house_rent,
            'medical' => $structure->medical_allowance,
            'conveyance' => $structure->conveyance_allowance,
            'other_allowances' => $structure->other_allowances,
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'leave_days' => $leaveDays,
            'holiday_days' => $holidayDays,
            'weekend_days' => $weekendDays,
            'working_days' => $workingDays,
            'total_late_minutes' => $totalLateMinutes,
            'total_ot_minutes' => $totalOtMinutes,
            'total_early_minutes' => $totalEarlyMinutes,
            'daily_rate' => $dailyRate,
            'per_minute_rate' => $perMinuteRate,
            'late_deduction' => $lateDeduction,
            'absent_deduction' => $absentDeduction,
            'ot_payable' => $otPayable,
            'bonus_amount' => $bonusAmount,
            'bonus_eligible_percent' => $bonusEligiblePercent,
            'tenure_months' => $tenureMonths,
            'advance_deduction' => $advanceDeduction,
            'tax_deduction' => $taxDeduction,
            'pf_deduction' => $pfDeduction,
            'net_payable' => $netPayable,
        ];
    }

    private function resolveSalaryRole($employee, $month, $tenantId)
    {
        $deptId = $employee->department_id;

        $assignment = DB::table('salary_role_assignments')
            ->where('tenant_id', $tenantId)
            ->where('applicable_month', $month)
            ->where(function ($q) use ($deptId) {
                $q->where('department_id', $deptId)
                  ->orWhereNull('department_id');
            })
            ->orderByRaw('CASE WHEN department_id = ? THEN 0 ELSE 1 END', [$deptId])
            ->first();

        if (!$assignment) return null;

        return SalaryRelation::find($assignment->salary_role_id);
    }

    private function shiftDailyHours($shift)
    {
        $start = strtotime($shift->start_time);
        $end = strtotime($shift->end_time);
        if ($end <= $start) $end += 86400;
        return ($end - $start) / 3600;
    }

    public function punchDataUpload()
    {
        $tenantId = $this->getTenantId();
        $liveSyncEnabled = DB::table('tenant_configs')
            ->where('tenant_id', $tenantId)
            ->where('group', 'payroll')
            ->where('key', 'punch_live_sync')
            ->value('value');

        $recentPunches = DB::table('raw_punch_data')
            ->where('tenant_id', $tenantId)
            ->orderBy('punch_date_time', 'desc')
            ->paginate(50);

        $liveCount = DB::table('attendance_logs')
            ->join('devices', 'attendance_logs.device_id', '=', 'devices.id')
            ->where('devices.tenant_id', $tenantId)
            ->count();

        $syncedCount = DB::table('raw_punch_data')
            ->where('tenant_id', $tenantId)
            ->where('source_file', 'like', 'live:%')
            ->count();

        return view('subscriber.payroll.punch-data-upload', compact(
            'recentPunches', 'liveSyncEnabled', 'liveCount', 'syncedCount'
        ));
    }

    public function processPunchUpload(Request $request)
    {
        $request->validate([
            'punch_file' => 'required|file|mimes:txt,csv,log,dat|max:10240',
        ]);

        $tenantId = $this->getTenantId();
        $file = $request->file('punch_file');
        $content = file_get_contents($file->getRealPath());
        $filename = $file->getClientOriginalName();

        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        $inserted = 0;
        $skipped = 0;
        $errors = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            if (str_starts_with($line, '#') || str_starts_with($line, '//')) continue;
            if (preg_match('/^(table|SN|GET|POST|Stamp|OpStamp|ErrorDelay|Delay|Realtime|TransTimes|TransInterval|TransFlag|TimeZone)/i', $line)) continue;

            $parsed = $this->parsePunchLine($line, $filename, $tenantId);
            if (!$parsed) {
                $skipped++;
                continue;
            }

            $employee = EmployeeProfile::where('tenant_id', $tenantId)
                ->where('employee_id', $parsed['employee_id'])
                ->first();

            try {
                DB::table('raw_punch_data')->insert([
                    'tenant_id' => $tenantId,
                    'employee_profile_id' => $employee?->id,
                    'employee_id' => $parsed['employee_id'],
                    'punch_machine_serial' => $parsed['serial'],
                    'punch_date_time' => $parsed['date_time'],
                    'status' => $parsed['status'],
                    'verify_type' => $parsed['verify_type'],
                    'source_file' => $filename,
                    'is_matched' => $employee ? true : false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $inserted++;
            } catch (\Exception $e) {
                $skipped++;
            }
        }

        $message = "Processed {$inserted} punch records";
        if ($skipped > 0) $message .= ", {$skipped} skipped";

        return redirect()->route('subscriber.payroll.punch-data-upload')
            ->with('success', $message);
    }

    protected function parsePunchLine(string $line, string $filename, int $tenantId): ?array
    {
        $fields = explode("\t", $line);

        if (count($fields) >= 2) {
            $pin = trim($fields[0]);
            $dateTime = trim($fields[1]);
            $status = $fields[2] ?? null;
            $verifyType = $fields[3] ?? null;

            if (!empty($pin) && $this->isValidDateTime($dateTime)) {
                return [
                    'employee_id' => $pin,
                    'date_time' => $dateTime,
                    'serial' => null,
                    'status' => $status ? trim($status) : null,
                    'verify_type' => $verifyType ? trim($verifyType) : null,
                ];
            }
        }

        $fields = preg_split('/[,\s]+/', $line);
        if (count($fields) >= 2) {
            $pin = trim($fields[0]);
            $dateTimeStr = trim($fields[1]);

            if (!empty($pin) && $this->isValidDateTime($dateTimeStr)) {
                return [
                    'employee_id' => $pin,
                    'date_time' => $dateTimeStr,
                    'serial' => $fields[2] ?? null,
                    'status' => $fields[3] ?? null,
                    'verify_type' => $fields[4] ?? null,
                ];
            }
        }

        return null;
    }

    protected function isValidDateTime(string $value): bool
    {
        $formats = ['Y-m-d H:i:s', 'Y-m-d H:i', 'd/m/Y H:i:s', 'd/m/Y H:i', 'm/d/Y H:i:s', 'Y/m/d H:i:s'];
        foreach ($formats as $format) {
            $d = \DateTime::createFromFormat($format, $value);
            if ($d && $d->format($format) === $value) return true;
        }
        return false;
    }

    public function toggleLiveSync(Request $request)
    {
        $tenantId = $this->getTenantId();
        $enabled = $request->boolean('enabled');

        DB::table('tenant_configs')->updateOrInsert(
            ['tenant_id' => $tenantId, 'group' => 'payroll', 'key' => 'punch_live_sync'],
            ['value' => $enabled ? '1' : '0', 'updated_at' => now()]
        );

        $status = $enabled ? 'enabled' : 'disabled';
        return redirect()->route('subscriber.payroll.punch-data-upload')
            ->with('success', "Live sync {$status}. New punches from ADMS will " . ($enabled ? 'now' : 'no longer') . " be synced to raw_punch_data.");
    }

    public function undoPunchData(Request $request)
    {
        $request->validate(['month' => 'required|date_format:Y-m']);
        $tenantId = $this->getTenantId();
        $month = $request->month;

        $count = DB::table('raw_punch_data')
            ->where('tenant_id', $tenantId)
            ->whereYear('punch_date_time', substr($month, 0, 4))
            ->whereMonth('punch_date_time', substr($month, 5, 2))
            ->delete();

        return redirect()->route('subscriber.payroll.punch-data-upload')
            ->with('success', "Removed {$count} raw punch record(s) for {$month}.");
    }

    public function monthPunchCount(Request $request)
    {
        $tenantId = $this->getTenantId();
        $month = $request->query('month');

        if (!$month || !preg_match('/^\d{4}-\d{2}$/', $month)) {
            return response()->json(['count' => 0]);
        }

        $count = DB::table('raw_punch_data')
            ->where('tenant_id', $tenantId)
            ->whereYear('punch_date_time', substr($month, 0, 4))
            ->whereMonth('punch_date_time', substr($month, 5, 2))
            ->count();

        return response()->json(['count' => $count]);
    }

    public function syncLivePunches()
    {
        $tenantId = $this->getTenantId();
        $count = 0;

        $logs = DB::table('attendance_logs')
            ->join('devices', 'attendance_logs.device_id', '=', 'devices.id')
            ->where('devices.tenant_id', $tenantId)
            ->select('attendance_logs.*', 'devices.serial_number', 'devices.tenant_id as dev_tenant_id')
            ->get();

        foreach ($logs as $log) {
            $alreadyExists = DB::table('raw_punch_data')
                ->where('tenant_id', $tenantId)
                ->where('employee_id', $log->pin)
                ->where('punch_date_time', $log->punched_at)
                ->exists();

            if ($alreadyExists) continue;

            $employee = EmployeeProfile::where('tenant_id', $tenantId)
                ->where('employee_id', $log->pin)
                ->first();

            DB::table('raw_punch_data')->insert([
                'tenant_id' => $tenantId,
                'employee_profile_id' => $employee?->id,
                'employee_id' => $log->pin,
                'punch_machine_serial' => $log->serial_number,
                'punch_date_time' => $log->punched_at,
                'status' => (string) $log->status,
                'verify_type' => (string) $log->verify_type,
                'source_file' => 'live:' . $log->serial_number,
                'is_matched' => $employee ? true : false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $count++;
        }

        return redirect()->route('subscriber.payroll.punch-data-upload')
            ->with('success', "Synced {$count} live punch records from ADMS to raw_punch_data.");
    }

    public function downloadTemplate($format)
    {
        $headers = [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="punch-template.' . $format . '"',
        ];

        if ($format === 'csv') {
            $content = "EmployeeID,DateTime,Status,VerifyType\n1,2026-07-27 08:15:00,0,1\n2,2026-07-27 08:20:30,0,1\n3,2026-07-27 17:30:00,1,1\n";
            $headers['Content-Type'] = 'text/csv';
        } else {
            $content = "1\t2026-07-27 08:15:00\t0\t1\n2\t2026-07-27 08:20:30\t0\t1\n3\t2026-07-27 17:30:00\t1\t1\n";
        }

        return response($content, 200, $headers);
    }

    public function payslip()
    {
        $tenantId = $this->getTenantId();
        $month = request('month', date('Y-m'));
        $departments = Department::where('tenant_id', $tenantId)->get();

        $salaryData = DB::table('salary_payroll')
            ->where('salary_payroll.tenant_id', $tenantId)
            ->where('salary_payroll.year_month', $month)
            ->leftJoin('employee_profiles', 'salary_payroll.employee_profile_id', '=', 'employee_profiles.id')
            ->leftJoin('users', 'employee_profiles.user_id', '=', 'users.id')
            ->leftJoin('departments', 'employee_profiles.department_id', '=', 'departments.id')
            ->leftJoin('salary_relations', 'salary_payroll.salary_role_id', '=', 'salary_relations.id')
            ->select(
                'salary_payroll.*',
                'users.name as emp_name',
                'employee_profiles.employee_id as emp_code',
                'employee_profiles.gender',
                'employee_profiles.phone_number',
                'departments.name as dept_name',
                'salary_relations.name as role_name'
            )
            ->orderBy('users.name')
            ->get();

        $stats = $salaryData->groupBy('status')->map(function ($items) {
            return [
                'count' => $items->count(),
                'total_net' => $items->sum('net_payable'),
            ];
        });

        return view('subscriber.payroll.payslip', compact('departments', 'salaryData', 'month', 'stats'));
    }

    public function processAttendance()
    {
        $tenantId = $this->getTenantId();
        $month = request('month', date('Y-m'));
        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);

        $months = DB::table('raw_punch_data')
            ->where('tenant_id', $tenantId)
            ->select(DB::raw("DATE_FORMAT(punch_date_time, '%Y-%m') as month"))
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month');

        $processedGroups = DB::table('attendance_processed')
            ->where('tenant_id', $tenantId)
            ->select(DB::raw("DATE_FORMAT(date, '%Y-%m') as month"), DB::raw('COUNT(*) as count'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get()
            ->keyBy('month');

        $records = DB::table('attendance_processed')
            ->where('attendance_processed.tenant_id', $tenantId)
            ->whereYear('attendance_processed.date', $year)
            ->whereMonth('attendance_processed.date', $monthNum)
            ->join('employee_profiles', 'attendance_processed.employee_profile_id', '=', 'employee_profiles.id')
            ->select('attendance_processed.*', 'employee_profiles.employee_id')
            ->orderBy('employee_id')
            ->orderBy('date')
            ->paginate(50);

        $stats = DB::table('attendance_processed')
            ->where('tenant_id', $tenantId)
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return view('subscriber.payroll.process-attendance', compact('months', 'month', 'processedGroups', 'records', 'stats'));
    }

    public function runProcessAttendance(Request $request)
    {
        $request->validate(['month' => 'required|date_format:Y-m']);
        $tenantId = $this->getTenantId();
        $month = $request->month;
        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);

        $employees = EmployeeProfile::where('tenant_id', $tenantId)->with('shift')->get();
        $startDate = "{$year}-{$monthNum}-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        // Get holidays and leave data
        $holidays = $this->getHolidays($tenantId, $year);
        $leaves = DB::table('leave_applications')
            ->where('tenant_id', $tenantId)
            ->where('status', 'approved')
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->get();

        $leaveLookup = [];
        foreach ($leaves as $l) {
            $s = max(strtotime($l->start_date), strtotime($startDate));
            $e = min(strtotime($l->end_date), strtotime($endDate));
            for ($d = $s; $d <= $e; $d += 86400) {
                $dateKey = date('Y-m-d', $d);
                $leaveLookup[$l->employee_profile_id][$dateKey] = $l;
            }
        }

        $punches = DB::table('raw_punch_data')
            ->where('tenant_id', $tenantId)
            ->whereYear('punch_date_time', $year)
            ->whereMonth('punch_date_time', $monthNum)
            ->orderBy('employee_id')
            ->orderBy('punch_date_time')
            ->get()
            ->groupBy(['employee_id', function ($p) { return substr($p->punch_date_time, 0, 10); }]);

        $processed = 0;
        $weekendDays = ['Friday']; // BD weekend

        DB::table('attendance_processed')
            ->where('tenant_id', $tenantId)
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->delete();

        foreach ($employees as $emp) {
            $empPunches = $punches->get($emp->employee_id);
            if (!$empPunches) continue;

            $shift = $emp->shift;
            $shiftStart = $shift ? $shift->start_time : '09:00:00';
            $shiftEnd = $shift ? $shift->end_time : '17:00:00';
            $lateBuffer = $shift ? ($shift->late_buffer_time ?? '00:30') : '00:30';

            foreach ($empPunches as $date => $dayPunches) {
                $dayName = date('l', strtotime($date));
                $isWeekend = in_array($dayName, $weekendDays);
                $isHoliday = in_array($date, $holidays);
                $hasLeave = isset($leaveLookup[$emp->id][$date]);

                $dayType = $isHoliday ? 'holiday' : ($isWeekend ? 'weekend' : 'working');
                $dayStatus = $hasLeave ? 'leave' : ($isHoliday ? 'holiday' : ($isWeekend ? 'weekend' : 'present'));

                $sorted = $dayPunches->sortBy('punch_date_time')->values();
                $punchCount = $sorted->count();
                $inTime = $sorted->first()->punch_date_time;
                $outTime = $sorted->last()->punch_date_time;

                $scheduledOut = $date . ' ' . $shiftEnd;

                $totalSeconds = 0;
                $lateMin = 0;
                $earlyMin = 0;
                $isLate = false;
                $isEarly = false;

                if ($dayStatus === 'present') {
                    $inTs = strtotime($inTime);
                    $outTs = strtotime($outTime);
                    $totalSeconds = max(0, $outTs - $inTs);

                    $shiftStartTs = strtotime($date . ' ' . $shiftStart);
                    $bufferSec = $this->timeToSeconds($lateBuffer);
                    if ($inTs > ($shiftStartTs + $bufferSec)) {
                        $lateMin = max(0, round(($inTs - $shiftStartTs) / 60));
                        $isLate = true;
                    }

                    $schedOutTs = strtotime($scheduledOut);
                    if ($outTs < $schedOutTs) {
                        $earlyMin = max(0, round(($schedOutTs - $outTs) / 60));
                        $isEarly = true;
                    }
                }

                $totalHours = $totalSeconds > 0 ? round($totalSeconds / 3600, 2) : 0;

                DB::table('attendance_processed')->insert([
                    'tenant_id' => $tenantId,
                    'employee_profile_id' => $emp->id,
                    'date' => $date,
                    'day_name' => $dayName,
                    'day_type' => $dayType,
                    'in_time' => $inTime,
                    'out_time' => $outTime,
                    'scheduled_out_time' => $scheduledOut,
                    'total_seconds' => $totalSeconds,
                    'total_hours' => $totalHours,
                    'late_minutes' => $lateMin,
                    'early_minutes' => $earlyMin,
                    'overtime_minutes' => 0,
                    'short_minutes' => $earlyMin,
                    'is_late' => $isLate,
                    'is_early' => $isEarly,
                    'status' => $dayStatus,
                    'leave_type' => $hasLeave ? 'Approved' : null,
                    'shift_name' => $shift?->name,
                    'punch_count' => $punchCount,
                    'source' => 'process',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $processed++;
            }
        }

        return redirect()->route('subscriber.payroll.process-attendance')
            ->with('success', "Processed {$processed} attendance records for {$month}.");
    }

    public function undoProcessedAttendance(Request $request)
    {
        $request->validate(['month' => 'required|date_format:Y-m']);
        $tenantId = $this->getTenantId();
        $month = $request->month;
        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);

        $count = DB::table('attendance_processed')
            ->where('tenant_id', $tenantId)
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->delete();

        return redirect()->route('subscriber.payroll.process-attendance', ['month' => $month])
            ->with('success', "Removed {$count} processed attendance record(s) for {$month}.");
    }

    public function processedMonthCount(Request $request)
    {
        $tenantId = $this->getTenantId();
        $month = $request->query('month');
        if (!$month || !preg_match('/^\d{4}-\d{2}$/', $month)) {
            return response()->json(['count' => 0]);
        }
        $count = DB::table('attendance_processed')
            ->where('tenant_id', $tenantId)
            ->whereYear('date', substr($month, 0, 4))
            ->whereMonth('date', substr($month, 5, 2))
            ->count();
        return response()->json(['count' => $count]);
    }

    protected function getHolidays($tenantId, $year)
    {
        return []; // Placeholder — extend when holiday module is implemented
    }

    protected function timeToSeconds($time)
    {
        $parts = explode(':', $time);
        return (int)$parts[0] * 3600 + (int)($parts[1] ?? 0) * 60 + (int)($parts[2] ?? 0);
    }

    public function report()
    {
        $tenantId = $this->getTenantId();
        $month = request('month', date('Y-m'));
        $tab = request('tab', 'overview');
        $departmentId = request('department_id');
        $employeeId = request('employee_profile_id');
        $monthLabel = date('F Y', strtotime($month . '-01'));

        $departments = Department::where('tenant_id', $tenantId)->orderBy('name')->get();
        $employees = EmployeeProfile::where('tenant_id', $tenantId)->with('user', 'department', 'designation')->orderBy('employee_id')->get();

        $salaryQuery = DB::table('salary_payroll')
            ->where('salary_payroll.tenant_id', $tenantId)
            ->where('salary_payroll.year_month', $month)
            ->leftJoin('employee_profiles', 'salary_payroll.employee_profile_id', '=', 'employee_profiles.id')
            ->leftJoin('users', 'employee_profiles.user_id', '=', 'users.id')
            ->leftJoin('departments', 'employee_profiles.department_id', '=', 'departments.id')
            ->leftJoin('designations', 'employee_profiles.designation_id', '=', 'designations.id')
            ->select(
                'salary_payroll.*', 'users.name as emp_name', 'employee_profiles.employee_id as emp_code',
                'employee_profiles.gender', 'departments.name as dept_name', 'designations.title as designation_name'
            );

        if ($departmentId) $salaryQuery->where('employee_profiles.department_id', $departmentId);
        if ($employeeId) $salaryQuery->where('salary_payroll.employee_profile_id', $employeeId);

        $salaryData = $salaryQuery->orderBy('users.name')->get();

        $attendanceQuery = DB::table('attendance_processed')
            ->where('attendance_processed.tenant_id', $tenantId)
            ->where(DB::raw("DATE_FORMAT(attendance_processed.date, '%Y-%m')"), $month)
            ->leftJoin('employee_profiles', 'attendance_processed.employee_profile_id', '=', 'employee_profiles.id')
            ->leftJoin('users', 'employee_profiles.user_id', '=', 'users.id')
            ->leftJoin('departments', 'employee_profiles.department_id', '=', 'departments.id')
            ->select(
                'attendance_processed.*', 'users.name as emp_name', 'employee_profiles.employee_id as emp_code',
                'departments.name as dept_name'
            );

        if ($departmentId) $attendanceQuery->where('employee_profiles.department_id', $departmentId);
        if ($employeeId) $attendanceQuery->where('attendance_processed.employee_profile_id', $employeeId);

        $attendanceData = $attendanceQuery->orderBy('attendance_processed.date', 'desc')->orderBy('users.name')->get();

        $punchQuery = DB::table('raw_punch_data')
            ->where('raw_punch_data.tenant_id', $tenantId)
            ->where(DB::raw("DATE_FORMAT(raw_punch_data.punch_date_time, '%Y-%m')"), $month)
            ->leftJoin('employee_profiles', 'raw_punch_data.employee_profile_id', '=', 'employee_profiles.id')
            ->leftJoin('users', 'employee_profiles.user_id', '=', 'users.id')
            ->select('raw_punch_data.*', 'users.name as emp_name', 'employee_profiles.employee_id as emp_code');

        if ($employeeId) $punchQuery->where('raw_punch_data.employee_profile_id', $employeeId);
        $punchData = $punchQuery->orderBy('punch_date_time', 'desc')->limit(500)->get();

        $leaveQuery = DB::table('leave_applications')
            ->where('leave_applications.tenant_id', $tenantId)
            ->leftJoin('employee_profiles', 'leave_applications.employee_profile_id', '=', 'employee_profiles.id')
            ->leftJoin('users', 'employee_profiles.user_id', '=', 'users.id')
            ->leftJoin('departments', 'employee_profiles.department_id', '=', 'departments.id')
            ->leftJoin('leave_types', 'leave_applications.leave_type_id', '=', 'leave_types.id')
            ->select(
                'leave_applications.*', 'users.name as emp_name', 'employee_profiles.employee_id as emp_code',
                'departments.name as dept_name', 'leave_types.name as leave_type_name'
            );

        if ($departmentId) $leaveQuery->where('employee_profiles.department_id', $departmentId);
        if ($employeeId) $leaveQuery->where('leave_applications.employee_profile_id', $employeeId);

        $leaveData = $leaveQuery->orderBy('start_date', 'desc')->get();

        $advanceQuery = DB::table('advances')
            ->where('advances.tenant_id', $tenantId)
            ->leftJoin('employee_profiles', 'advances.employee_profile_id', '=', 'employee_profiles.id')
            ->leftJoin('users', 'employee_profiles.user_id', '=', 'users.id')
            ->leftJoin('departments', 'employee_profiles.department_id', '=', 'departments.id')
            ->leftJoin('advance_types', 'advances.advance_type_id', '=', 'advance_types.id')
            ->select(
                'advances.*', 'users.name as emp_name', 'employee_profiles.employee_id as emp_code',
                'departments.name as dept_name', 'advance_types.name as advance_type_name'
            );

        if ($departmentId) $advanceQuery->where('employee_profiles.department_id', $departmentId);
        if ($employeeId) $advanceQuery->where('advances.employee_profile_id', $employeeId);

        $advanceData = $advanceQuery->orderBy('advances.created_at', 'desc')->get();

        $deptSalarySummary = $salaryData->groupBy('dept_name')->map(function ($items, $deptName) {
            return [
                'dept_name' => $deptName ?: 'N/A',
                'count' => $items->count(),
                'total_gross' => $items->sum('gross_salary'),
                'total_net' => $items->sum('net_payable'),
                'total_bonus' => $items->sum('bonus_amount'),
                'total_deduction' => $items->sum('late_deduction') + $items->sum('absent_deduction') + $items->sum('tax_deduction') + $items->sum('pf_deduction'),
                'avg_net' => $items->avg('net_payable'),
            ];
        })->values();

        return view('subscriber.payroll.report', compact(
            'month', 'monthLabel', 'tab', 'departments', 'employees', 'departmentId', 'employeeId',
            'salaryData', 'attendanceData', 'punchData', 'leaveData', 'advanceData', 'deptSalarySummary'
        ));
    }

    private function getAttendanceData($month, $departmentId = null, $employeeId = null)
    {
        $tenantId = $this->getTenantId();
        $query = DB::table('attendance_processed')
            ->where('attendance_processed.tenant_id', $tenantId)
            ->where(DB::raw("DATE_FORMAT(attendance_processed.date, '%Y-%m')"), $month)
            ->leftJoin('employee_profiles', 'attendance_processed.employee_profile_id', '=', 'employee_profiles.id')
            ->leftJoin('users', 'employee_profiles.user_id', '=', 'users.id')
            ->leftJoin('departments', 'employee_profiles.department_id', '=', 'departments.id')
            ->select('attendance_processed.*', 'users.name as emp_name', 'employee_profiles.employee_id as emp_code', 'departments.name as dept_name');
        if ($departmentId) $query->where('employee_profiles.department_id', $departmentId);
        if ($employeeId) $query->where('attendance_processed.employee_profile_id', $employeeId);
        return $query->orderBy('attendance_processed.date', 'desc')->orderBy('users.name')->get();
    }

    private function getPunchData($month, $employeeId = null)
    {
        $tenantId = $this->getTenantId();
        $query = DB::table('raw_punch_data')
            ->where('raw_punch_data.tenant_id', $tenantId)
            ->where(DB::raw("DATE_FORMAT(raw_punch_data.punch_date_time, '%Y-%m')"), $month)
            ->leftJoin('employee_profiles', 'raw_punch_data.employee_profile_id', '=', 'employee_profiles.id')
            ->leftJoin('users', 'employee_profiles.user_id', '=', 'users.id')
            ->select('raw_punch_data.*', 'users.name as emp_name', 'employee_profiles.employee_id as emp_code');
        if ($employeeId) $query->where('raw_punch_data.employee_profile_id', $employeeId);
        return $query->orderBy('raw_punch_data.punch_date_time', 'desc')->limit(500)->get();
    }

    private function getLeaveData($departmentId = null, $employeeId = null)
    {
        $tenantId = $this->getTenantId();
        $query = DB::table('leave_applications')
            ->where('leave_applications.tenant_id', $tenantId)
            ->leftJoin('employee_profiles', 'leave_applications.employee_profile_id', '=', 'employee_profiles.id')
            ->leftJoin('users', 'employee_profiles.user_id', '=', 'users.id')
            ->leftJoin('departments', 'employee_profiles.department_id', '=', 'departments.id')
            ->leftJoin('leave_types', 'leave_applications.leave_type_id', '=', 'leave_types.id')
            ->select('leave_applications.*', 'users.name as emp_name', 'employee_profiles.employee_id as emp_code', 'departments.name as dept_name', 'leave_types.name as leave_type_name');
        if ($departmentId) $query->where('employee_profiles.department_id', $departmentId);
        if ($employeeId) $query->where('leave_applications.employee_profile_id', $employeeId);
        return $query->orderBy('leave_applications.start_date', 'desc')->get();
    }

    private function getAdvanceData($departmentId = null, $employeeId = null)
    {
        $tenantId = $this->getTenantId();
        $query = DB::table('advances')
            ->where('advances.tenant_id', $tenantId)
            ->leftJoin('employee_profiles', 'advances.employee_profile_id', '=', 'employee_profiles.id')
            ->leftJoin('users', 'employee_profiles.user_id', '=', 'users.id')
            ->leftJoin('departments', 'employee_profiles.department_id', '=', 'departments.id')
            ->leftJoin('advance_types', 'advances.advance_type_id', '=', 'advance_types.id')
            ->select('advances.*', 'users.name as emp_name', 'employee_profiles.employee_id as emp_code', 'departments.name as dept_name', 'advance_types.name as advance_type_name');
        if ($departmentId) $query->where('employee_profiles.department_id', $departmentId);
        if ($employeeId) $query->where('advances.employee_profile_id', $employeeId);
        return $query->orderBy('advances.created_at', 'desc')->get();
    }

    private function getSalaryData($month, $departmentId = null, $employeeId = null)
    {
        $tenantId = $this->getTenantId();
        $query = DB::table('salary_payroll')
            ->where('salary_payroll.tenant_id', $tenantId)
            ->where('salary_payroll.year_month', $month)
            ->leftJoin('employee_profiles', 'salary_payroll.employee_profile_id', '=', 'employee_profiles.id')
            ->leftJoin('users', 'employee_profiles.user_id', '=', 'users.id')
            ->leftJoin('departments', 'employee_profiles.department_id', '=', 'departments.id')
            ->leftJoin('designations', 'employee_profiles.designation_id', '=', 'designations.id')
            ->select(
                'salary_payroll.*', 'users.name as emp_name', 'employee_profiles.employee_id as emp_code',
                'employee_profiles.gender', 'employee_profiles.joining_date',
                'departments.name as dept_name', 'designations.title as designation_name'
            );

        if ($departmentId) $query->where('employee_profiles.department_id', $departmentId);
        if ($employeeId) $query->where('salary_payroll.employee_profile_id', $employeeId);

        return $query->orderBy('departments.name')->orderBy('users.name')->get();
    }

    public function reportExport($type)
    {
        $month = request('month', date('Y-m'));
        $departmentId = request('department_id');
        $employeeId = request('employee_profile_id');
        $format = request('format', 'pdf');
        $tab = request('tab', 'salary');
        $monthLabel = date('F Y', strtotime($month . '-01'));

        $salaryData = $this->getSalaryData($month, $departmentId, $employeeId);
        $attendanceData = $this->getAttendanceData($month, $departmentId, $employeeId);
        $punchData = $this->getPunchData($month, $employeeId);
        $leaveData = $this->getLeaveData($departmentId, $employeeId);
        $advanceData = $this->getAdvanceData($departmentId, $employeeId);
        $deptSalarySummary = $salaryData->groupBy('dept_name')->map(function ($items, $deptName) {
            return [
                'dept_name' => $deptName ?: 'N/A', 'count' => $items->count(),
                'total_gross' => $items->sum('gross_salary'), 'total_net' => $items->sum('net_payable'),
                'total_bonus' => $items->sum('bonus_amount'),
                'total_deduction' => $items->sum('late_deduction') + $items->sum('absent_deduction') + $items->sum('tax_deduction') + $items->sum('pf_deduction'),
                'avg_net' => $items->avg('net_payable'),
            ];
        })->values();

        $viewMap = [
            'employee' => 'subscriber.payroll.exports.employee-pdf',
            'department' => 'subscriber.payroll.exports.department-pdf',
            'punch' => 'subscriber.payroll.exports.punch-pdf',
            'leave' => 'subscriber.payroll.exports.leave-pdf',
            'timecard' => 'subscriber.payroll.exports.timecard-pdf',
            'salary' => 'subscriber.payroll.exports.salary-pdf',
            'advance' => 'subscriber.payroll.exports.advance-pdf',
            'overview' => 'subscriber.payroll.exports.overview-pdf',
        ];

        $csvHeaders = [
            'employee' => ['Employee ID', 'Name', 'Department', 'Designation', 'Gross', 'Present', 'Absent', 'Late (min)', 'Late Ded', 'Tax', 'PF', 'Bonus', 'OT', 'Advance', 'Net', 'Status'],
            'department' => ['Department', 'Employees', 'Total Gross', 'Total Bonus', 'Total Deduction', 'Total Net', 'Avg Net'],
            'punch' => ['Date', 'Time', 'Employee', 'PIN', 'Status', 'Verify', 'Source'],
            'leave' => ['Employee', 'Department', 'Leave Type', 'From', 'To', 'Days', 'Reason', 'Status'],
            'timecard' => ['Date', 'Employee', 'Department', 'Shift', 'In', 'Out', 'Hours', 'Late', 'Early', 'OT', 'Status'],
            'salary' => ['#', 'Employee ID', 'Name', 'Department', 'Designation', 'Basic', 'House', 'Medical', 'Conv', 'Other', 'Gross', 'Present', 'Absent', 'Leave', 'Late (min)', 'Late Ded', 'Abs Ded', 'Tax', 'PF', 'Bonus', 'OT', 'Advance', 'Net', 'Status'],
            'advance' => ['Employee', 'Department', 'Type', 'Amount', 'Approved', 'Installments', 'Monthly Ded', 'Status', 'Date'],
        ];

        $csvData = [
            'employee' => $salaryData->map(fn($s) => [$s->emp_code, $s->emp_name, $s->dept_name, $s->designation_name, $s->gross_salary, $s->present_days, $s->absent_days, $s->total_late_minutes, $s->late_deduction, $s->tax_deduction, $s->pf_deduction, $s->bonus_amount, $s->ot_payable, $s->advance_deduction, $s->net_payable, ucfirst($s->status)]),
            'department' => $deptSalarySummary->map(fn($d) => [$d['dept_name'], $d['count'], $d['total_gross'], $d['total_bonus'], $d['total_deduction'], $d['total_net'], round($d['avg_net'])]),
            'punch' => $punchData->map(fn($p) => [date('d M Y', strtotime($p->punch_date_time)), date('h:i A', strtotime($p->punch_date_time)), $p->emp_name, $p->employee_id, $p->status == 0 ? 'Check In' : 'Check Out', $p->verify_type, $p->source ?? 'ADMS']),
            'leave' => $leaveData->map(fn($l) => [$l->emp_name, $l->dept_name, $l->leave_type_name ?? 'N/A', $l->start_date, $l->end_date, $l->total_days, $l->reason, ucfirst($l->status)]),
            'timecard' => $attendanceData->map(fn($a) => [date('d M Y', strtotime($a->date)), $a->emp_name, $a->dept_name, $a->shift_name, $a->in_time ? date('h:i A', strtotime($a->in_time)) : '—', $a->out_time ? date('h:i A', strtotime($a->out_time)) : '—', round($a->total_hours, 2), $a->late_minutes, $a->early_minutes, $a->overtime_minutes, ucfirst($a->status)]),
            'salary' => $salaryData->values()->map(fn($s, $i) => [$i + 1, $s->emp_code, $s->emp_name, $s->dept_name, $s->designation_name, $s->basic_salary, $s->house_rent, $s->medical, $s->conveyance, $s->other_allowances, $s->gross_salary, $s->present_days, $s->absent_days, $s->leave_days, $s->total_late_minutes, $s->late_deduction, $s->absent_deduction, $s->tax_deduction, $s->pf_deduction, $s->bonus_amount, $s->ot_payable, $s->advance_deduction, $s->net_payable, ucfirst($s->status)]),
            'advance' => $advanceData->map(fn($a) => [$a->emp_name, $a->dept_name, $a->advance_type_name ?? 'N/A', $a->amount, $a->approved_amount, $a->installments, $a->monthly_deduction, ucfirst($a->status), date('d M Y', strtotime($a->created_at))]),
        ];

        $fileName = $tab . '-report-' . $month;

        if ($format === 'csv' || $format === 'excel') {
            $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="' . $fileName . '.csv"'];
            $callback = function () use ($csvHeaders, $csvData, $tab, $monthLabel) {
                $file = fopen('php://output', 'w');
                fputcsv($file, [ucfirst($tab) . ' Report - ' . $monthLabel]);
                fputcsv($file, []);
                fputcsv($file, $csvHeaders[$tab] ?? []);
                foreach (($csvData[$tab] ?? []) as $row) { fputcsv($file, $row); }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        $view = $viewMap[$tab] ?? $viewMap['salary'];
        $data = compact('month', 'monthLabel', 'salaryData', 'attendanceData', 'punchData', 'leaveData', 'advanceData', 'deptSalarySummary', 'tab');

        if (view()->exists($view)) {
            $html = view($view, $data)->render();
        } else {
            $html = view('subscriber.payroll.exports.fallback-pdf', $data + ['title' => ucfirst($tab) . ' Report'])->render();
        }

        return response($html, 200, ['Content-Type' => 'text/html', 'Content-Disposition' => 'inline; filename="' . $fileName . '.html"']);
    }
}
