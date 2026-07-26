<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\LeaveApplication;
use App\Models\EmployeeProfile;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LeaveSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        if (!$tenant) return;

        // Create leave types with accrual
        $types = [
            ['name' => 'Casual Leave', 'code' => 'CL', 'days_per_year' => 10, 'accrual_enabled' => true],
            ['name' => 'Sick Leave', 'code' => 'SL', 'days_per_year' => 14, 'accrual_enabled' => true],
            ['name' => 'Earned Leave', 'code' => 'EL', 'days_per_year' => 20, 'accrual_enabled' => true],
            ['name' => 'Leave Without Pay', 'code' => 'LWP', 'days_per_year' => 0, 'accrual_enabled' => false],
            ['name' => 'Maternity Leave', 'code' => 'ML', 'days_per_year' => 120, 'accrual_enabled' => false],
        ];

        $createdTypes = [];
        foreach ($types as $t) {
            $createdTypes[$t['code']] = LeaveType::firstOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $t['code']],
                $t
            );
        }

        $employees = EmployeeProfile::with('user')->where('tenant_id', $tenant->id)->get();
        if ($employees->isEmpty()) return;

        $year = now()->year;
        $balanceCount = 0;
        $appCount = 0;

        foreach ($employees as $emp) {
            foreach ($createdTypes as $code => $type) {
                // Calculate pro-rata allocation
                $daysPerYear = (float) $type->days_per_year;
                if ($type->accrual_enabled && $emp->joining_date) {
                    $joining = Carbon::parse($emp->joining_date);
                    $monthsEmployed = max(1, $joining->diffInMonths(now()));
                    $allocated = round(($daysPerYear / 12) * $monthsEmployed, 1);
                } else {
                    $allocated = $daysPerYear;
                }

                $spent = 0;
                // Create some approved leave applications
                if (rand(0, 1) && $code !== 'EL') {
                    $days = rand(1, min(3, (int) $allocated));
                    $start = now()->subMonths(rand(1, 4))->startOfMonth()->addDays(rand(0, 20));
                    $end = $start->copy()->addDays($days - 1);

                    LeaveApplication::create([
                        'tenant_id' => $tenant->id,
                        'employee_profile_id' => $emp->id,
                        'leave_type_id' => $type->id,
                        'start_date' => $start,
                        'end_date' => $end,
                        'total_days' => $days,
                        'reason' => collect([
                            'Medical appointment',
                            'Family emergency',
                            'Personal errand',
                            'Not feeling well',
                            'Doctor\'s appointment',
                        ])->random(),
                        'status' => 'approved',
                        'actioned_by' => User::first()?->id,
                        'action_remarks' => 'Approved.',
                    ]);
                    $spent = $days;
                    $appCount++;
                }

                LeaveBalance::firstOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'employee_profile_id' => $emp->id,
                        'leave_type_id' => $type->id,
                        'calendar_year' => $year,
                    ],
                    [
                        'allocated_days' => $allocated,
                        'spent_days' => $spent,
                        'earned_days' => 0,
                    ]
                );
                $balanceCount++;
            }
        }

        $this->command->info("Seeded {$balanceCount} leave balances + {$appCount} applications across {$employees->count()} employees.");
    }
}
