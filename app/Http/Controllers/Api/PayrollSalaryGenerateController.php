<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollSalaryGenerateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $month = $request->month ?? now()->format('Y-m');

        $payrolls = DB::table('salary_payroll')
            ->where('tenant_id', $tenantId)
            ->where('salary_month', $month)
            ->get();

        return response()->json($payrolls);
    }

    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $tenantId = $request->user()->tenant_id;
        $month = $validated['month'];

        $exists = DB::table('salary_payroll')
            ->where('tenant_id', $tenantId)
            ->where('salary_month', $month)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Salary already generated for this month'], 409);
        }

        $employees = DB::table('employee_profiles')
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->get();

        $generated = 0;
        foreach ($employees as $emp) {
            $structure = DB::table('salary_structures')
                ->where('employee_profile_id', $emp->id)
                ->first();

            $gross = $structure?->gross_salary ?? 0;
            $deductions = $structure?->total_deductions ?? 0;
            $net = $gross - $deductions;

            DB::table('salary_payroll')->insert([
                'tenant_id' => $tenantId,
                'employee_id' => $emp->employee_id,
                'salary_month' => $month,
                'gross_salary' => $gross,
                'total_deductions' => $deductions,
                'net_payable' => $net,
                'status' => 'generated',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $generated++;
        }

        return response()->json(['message' => "Salary generated for {$generated} employees", 'count' => $generated], 201);
    }

    public function confirm(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $tenantId = $request->user()->tenant_id;

        $updated = DB::table('salary_payroll')
            ->where('tenant_id', $tenantId)
            ->where('salary_month', $validated['month'])
            ->update(['status' => 'confirmed', 'updated_at' => now()]);

        return response()->json(['message' => "{$updated} payroll records confirmed"]);
    }

    public function undo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $tenantId = $request->user()->tenant_id;

        $deleted = DB::table('salary_payroll')
            ->where('tenant_id', $tenantId)
            ->where('salary_month', $validated['month'])
            ->delete();

        return response()->json(['message' => "{$deleted} payroll records undone"]);
    }
}
