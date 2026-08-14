<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalaryRoleAssignment;
use App\Models\SalaryStructure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function salaryRoles(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $roles = SalaryRoleAssignment::with(['salaryRole', 'department'])
            ->where('tenant_id', $tenantId)
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'role' => $r->salaryRole?->name,
                'department' => $r->department?->name ?? 'All',
                'month' => $r->applicable_month,
            ]);

        return response()->json($roles);
    }

    public function payslips(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $payrolls = DB::table('salary_payroll')
            ->where('tenant_id', $tenantId)
            ->when($request->month, fn($q) => $q->where('salary_month', $request->month))
            ->when($request->employee_id, fn($q) => $q->where('employee_id', $request->employee_id))
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 15);

        $payrolls->getCollection()->transform(function ($p) {
            return [
                'id' => $p->id,
                'employee_id' => $p->employee_id,
                'month' => $p->salary_month,
                'gross' => $p->gross_salary,
                'deductions' => $p->total_deductions,
                'net' => $p->net_payable,
                'status' => $p->status,
                'generated_at' => $p->created_at?->format('Y-m-d H:i'),
            ];
        });

        return response()->json($payrolls);
    }

    public function generate(Request $request): JsonResponse
    {
        return response()->json(['message' => 'Salary generation must be done via the web interface'], 200);
    }

    public function reports(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $month = $request->month ?? now()->format('Y-m');

        $summary = DB::table('salary_payroll')
            ->where('tenant_id', $tenantId)
            ->where('salary_month', $month)
            ->select(
                DB::raw('COUNT(*) as total_employees'),
                DB::raw('SUM(gross_salary) as total_gross'),
                DB::raw('SUM(total_deductions) as total_deductions'),
                DB::raw('SUM(net_payable) as total_net')
            )
            ->first();

        $byDepartment = DB::table('salary_payroll as sp')
            ->join('employee_profiles as ep', 'sp.employee_id', '=', 'ep.employee_id')
            ->join('departments as d', 'ep.department_id', '=', 'd.id')
            ->where('sp.tenant_id', $tenantId)
            ->where('sp.salary_month', $month)
            ->select('d.name as department', DB::raw('COUNT(*) as count'), DB::raw('SUM(sp.net_payable) as total'))
            ->groupBy('d.name')
            ->get();

        return response()->json([
            'summary' => $summary,
            'by_department' => $byDepartment,
        ]);
    }
}
