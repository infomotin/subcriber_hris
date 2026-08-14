<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollProcessAttendanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $month = $request->month ?? now()->format('Y-m');

        $processed = DB::table('attendance_processed')
            ->where('tenant_id', $tenantId)
            ->where('attendance_month', $month)
            ->get();

        return response()->json($processed);
    }

    public function process(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $tenantId = $request->user()->tenant_id;
        $month = $validated['month'];

        $count = DB::table('raw_punch_data')
            ->where('tenant_id', $tenantId)
            ->whereRaw("DATE_FORMAT(punch_date, '%Y-%m') = ?", [$month])
            ->count();

        DB::table('attendance_processed')->updateOrInsert(
            ['tenant_id' => $tenantId, 'attendance_month' => $month],
            [
                'total_punches' => $count,
                'status' => 'processed',
                'processed_at' => now(),
                'processed_by' => $request->user()->id,
                'updated_at' => now(),
            ]
        );

        return response()->json(['message' => "Attendance processed for {$month}", 'total_punches' => $count]);
    }

    public function undo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $tenantId = $request->user()->tenant_id;

        DB::table('attendance_processed')
            ->where('tenant_id', $tenantId)
            ->where('attendance_month', $validated['month'])
            ->delete();

        return response()->json(['message' => "Attendance processing undone for {$validated['month']}"]);
    }
}
