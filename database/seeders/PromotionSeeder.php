<?php

namespace Database\Seeders;

use App\Models\EmployeePromotion;
use App\Models\EmployeeProfile;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        if (!$tenant) {
            $this->command->warn('No tenant found.');
            return;
        }

        $employees = EmployeeProfile::with(['user', 'department', 'designation'])->where('tenant_id', $tenant->id)->get();
        if ($employees->isEmpty()) {
            $this->command->warn('No employees found.');
            return;
        }

        $depts = Department::where('tenant_id', $tenant->id)->get()->keyBy('id');
        $desigs = Designation::where('tenant_id', $tenant->id)->get()->keyBy('id');

        $promotionsData = [
            // EMP2026002: Nusrat Jahan — HR Associate → HR Manager (dept unchanged)
            [
                'emp_id' => 'EMP2026002',
                'old_desig' => 'Administrative Officer',
                'new_desig' => 'HR Manager',
                'old_dept' => 'Human Resources',
                'new_dept' => 'Human Resources',
                'type' => 'merit',
                'date' => '2026-01-15',
                'notes' => 'Promoted to HR Manager based on exceptional performance in recruitment and employee relations.',
            ],
            // EMP2026001: Adnan Rahman — Software Engineer → Senior Software Engineer
            [
                'emp_id' => 'EMP2026001',
                'old_desig' => 'Software Engineer',
                'new_desig' => 'Senior Software Engineer',
                'old_dept' => 'Information Technology',
                'new_dept' => 'Information Technology',
                'type' => 'merit',
                'date' => '2025-07-01',
                'notes' => 'Merit-based promotion for outstanding technical contributions and project delivery.',
            ],
            // EMP2026006: Rafiq Hasan — transferred from Administration to Operations
            [
                'emp_id' => 'EMP2026006',
                'old_desig' => 'Administrative Officer',
                'new_desig' => 'Support Executive',
                'old_dept' => 'Administration',
                'new_dept' => 'Operations & Support',
                'type' => 'departmental',
                'date' => '2026-03-01',
                'notes' => 'Departmental transfer to strengthen Operations team with administrative expertise.',
            ],
            // EMP2026007: Shamim Reza — Jr. → Software Engineer (positional)
            [
                'emp_id' => 'EMP2026007',
                'old_desig' => 'Junior Software Engineer',
                'new_desig' => 'Software Engineer',
                'old_dept' => 'Information Technology',
                'new_dept' => 'Information Technology',
                'type' => 'positional',
                'date' => '2025-10-01',
                'notes' => 'Positional upgrade after successful completion of probation and training period.',
            ],
            // EMP2026011: Mehedi Hasan — Junior Software Engineer → Software Engineer
            [
                'emp_id' => 'EMP2026011',
                'old_desig' => 'Junior Software Engineer',
                'new_desig' => 'Software Engineer',
                'old_dept' => 'Information Technology',
                'new_dept' => 'Information Technology',
                'type' => 'merit',
                'date' => '2026-06-01',
                'notes' => 'Fast-track promotion due to exceptional coding skills and project ownership.',
            ],
            // EMP2026015: Shahidul Islam — Senior position change
            [
                'emp_id' => 'EMP2026015',
                'old_desig' => 'R&D Engineer',
                'new_desig' => 'Team Lead',
                'old_dept' => 'Research & Development',
                'new_dept' => 'Research & Development',
                'type' => 'seniority',
                'date' => '2026-04-01',
                'notes' => 'Seniority-based promotion recognizing 5+ years of dedicated service and leadership potential.',
            ],
            // EMP2026010: Jahanara Begum — QA Engineer → Team Lead
            [
                'emp_id' => 'EMP2026010',
                'old_desig' => 'QA Engineer',
                'new_desig' => 'Team Lead',
                'old_dept' => 'Quality Assurance',
                'new_dept' => 'Quality Assurance',
                'type' => 'special',
                'date' => '2026-07-01',
                'notes' => 'Special achievement award for implementing automated testing framework, saving 40% QA time.',
            ],
            // EMP2026020: Morshed Alam — Business Analyst → Senior (remains BA, seniority)
            [
                'emp_id' => 'EMP2026020',
                'old_desig' => 'Business Analyst',
                'new_desig' => 'Team Lead',
                'old_dept' => 'Information Technology',
                'new_dept' => 'Information Technology',
                'type' => 'seniority',
                'date' => '2026-02-01',
                'notes' => 'Leadership promotion based on consistent delivery of critical business analysis.',
            ],
            // EMP2026005: Sajid Islam — Support → transferred to Sales (career growth)
            [
                'emp_id' => 'EMP2026005',
                'old_desig' => 'Support Executive',
                'new_desig' => 'Sales Executive',
                'old_dept' => 'Operations & Support',
                'new_dept' => 'Sales & Marketing',
                'type' => 'departmental',
                'date' => '2026-05-15',
                'notes' => 'Cross-functional move leveraging product knowledge for sales career growth.',
            ],
            // EMP2026003: Tanvir Ahmed — CTO grade already top, positional stay
            [
                'emp_id' => 'EMP2026003',
                'old_desig' => 'Chief Technology Officer',
                'new_desig' => 'Chief Technology Officer',
                'old_dept' => 'Information Technology',
                'new_dept' => 'Information Technology',
                'type' => 'special',
                'date' => '2025-12-01',
                'notes' => 'Special recognition for successful digital transformation initiatives.',
            ],
        ];

        $count = 0;
        foreach ($promotionsData as $data) {
            $emp = $employees->firstWhere('employee_id', $data['emp_id']);
            if (!$emp) continue;

            $oldDept = $depts->firstWhere('name', $data['old_dept']);
            $newDept = $depts->firstWhere('name', $data['new_dept']);
            $oldDesig = $desigs->firstWhere('title', $data['old_desig']);
            $newDesig = $desigs->firstWhere('title', $data['new_desig']);

            if (!$oldDept || !$newDept || !$oldDesig || !$newDesig) continue;

            $ref = 'PRO-' . strtoupper($tenant->code ?? 'XX') . '-' . date('Ymd', strtotime($data['date'])) . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

            EmployeePromotion::create([
                'tenant_id' => $tenant->id,
                'employee_profile_id' => $emp->id,
                'old_department_id' => $oldDept->id,
                'new_department_id' => $newDept->id,
                'old_designation_id' => $oldDesig->id,
                'new_designation_id' => $newDesig->id,
                'promotion_type' => $data['type'],
                'notes' => $data['notes'],
                'effective_date' => $data['date'],
                'status' => 'active',
                'reference_number' => $ref,
            ]);

            $count++;
        }

        $this->command->info("Seeded {$count} promotion(s).");
    }
}
