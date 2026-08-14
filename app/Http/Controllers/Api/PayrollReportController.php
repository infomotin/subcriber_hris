<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollReportController extends Controller
{
    public function index(Request $request): JsonResponse
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

    public function export(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $month = $request->month ?? now()->format('Y-m');

        $records = DB::table('salary_payroll')
            ->where('tenant_id', $tenantId)
            ->where('salary_month', $month)
            ->get();

        $csv = "Employee ID,Gross Salary,Deductions,Net Payable,Status\n";
        foreach ($records as $r) {
            $csv .= "{$r->employee_id},{$r->gross_salary},{$r->total_deductions},{$r->net_payable},{$r->status}\n";
        }

        return response()->json(['csv' => $csv, 'count' => $records->count()]);
    }
}
