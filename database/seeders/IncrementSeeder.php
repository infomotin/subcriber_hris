<?php

namespace Database\Seeders;

use App\Models\Increment;
use App\Models\IncrementRule;
use App\Models\EmployeeProfile;
use App\Models\Tenant;
use App\Models\SalaryStructure;
use Illuminate\Database\Seeder;

class IncrementSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        if (!$tenant) {
            $this->command->warn('No tenant found. Skipping increment seeding.');
            return;
        }

        $rules = IncrementRule::where('tenant_id', $tenant->id)->get()->keyBy('name');
        $employees = EmployeeProfile::with('salaryStructure')->where('tenant_id', $tenant->id)->get();

        if ($employees->isEmpty()) {
            $this->command->warn('No employees found. Skipping increment seeding.');
            return;
        }

        $now = now();
        $types = ['annual', 'special', 'manual', 'bulk'];
        $statuses = ['enforced', 'enforced', 'pending']; // 2/3 enforced
        $basedOnOptions = ['basic', 'gross'];
        $enforcedCount = 0;
        $pendingCount = 0;

        foreach ($employees as $emp) {
            if (!$emp->salaryStructure) continue;

            $numIncrements = rand(0, 2);
            for ($i = 0; $i < $numIncrements; $i++) {
                $type = $types[array_rand($types)];
                $status = $statuses[array_rand($statuses)];

                $basedOn = $basedOnOptions[array_rand($basedOnOptions)];
                $pct = rand(3, 12);

                $oldBasic = $emp->salaryStructure->basic_salary;
                $oldGross = $emp->salaryStructure->basic_salary
                    + $emp->salaryStructure->house_rent
                    + $emp->salaryStructure->medical_allowance
                    + $emp->salaryStructure->conveyance_allowance
                    + ($emp->salaryStructure->other_allowances ?? 0);

                $baseValue = $basedOn === 'gross' ? $oldGross : $oldBasic;
                $incAmount = round($baseValue * ($pct / 100), 2);

                $newBasic = $oldBasic + ($basedOn === 'basic' ? $incAmount : 0);
                $newGross = $oldGross + $incAmount;

                $rule = null;
                if ($type === 'annual') {
                    $rule = $rules->get('Annual Increment FY 2026-27') ?? $rules->first();
                } elseif ($type === 'special') {
                    $rule = $rules->get('Special Performance Bonus') ?? $rules->first();
                }

                $enforcedAt = match($status) {
                    'enforced' => $now->copy()->subMonths(rand(1, 10)),
                    default => null,
                };

                $ref = 'INC-' . strtoupper(substr(md5($emp->id . $i . rand()), 0, 8));

                $increment = Increment::create([
                    'tenant_id' => $tenant->id,
                    'employee_profile_id' => $emp->id,
                    'increment_rule_id' => $rule?->id,
                    'increment_type' => $type,
                    'old_basic' => $oldBasic,
                    'old_gross' => $oldGross,
                    'new_basic' => $newBasic,
                    'new_gross' => $newGross,
                    'increment_amount' => $incAmount,
                    'increment_percentage' => $pct,
                    'based_on' => $basedOn,
                    'status' => $status,
                    'enforced_at' => $enforcedAt,
                    'enforced_by' => $status === 'enforced' ? 'HR Admin' : null,
                    'reference_number' => $ref,
                    'notes' => match($type) {
                        'annual' => 'Annual increment for performance year',
                        'special' => 'Special merit-based increment',
                        'manual' => 'Manual salary adjustment',
                        'bulk' => 'Bulk department-wide adjustment',
                        default => 'Salary increment',
                    },
                ]);

                if ($status === 'enforced') {
                    $enforcedCount++;
                    if ($emp->salaryStructure) {
                        $grossDiff = $newGross - $oldGross;
                        $emp->salaryStructure->update([
                            'basic_salary' => $newBasic,
                            'house_rent' => $emp->salaryStructure->house_rent + round($grossDiff * 0.25, 2),
                            'medical_allowance' => $emp->salaryStructure->medical_allowance + round($grossDiff * 0.10, 2),
                            'conveyance_allowance' => $emp->salaryStructure->conveyance_allowance + round($grossDiff * 0.15, 2),
                        ]);
                    }
                } else {
                    $pendingCount++;
                }
            }
        }

        $this->command->info("Seeded {$enforcedCount} enforced + {$pendingCount} pending increment(s) across {$employees->count()} employees.");
    }
}
