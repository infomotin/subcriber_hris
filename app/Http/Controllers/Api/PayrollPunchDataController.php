<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollPunchDataController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $punches = DB::table('raw_punch_data')
            ->where('tenant_id', $tenantId)
            ->when($request->date_from, fn($q) => $q->whereDate('punch_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('punch_date', '<=', $request->date_to))
            ->when($request->employee_id, fn($q) => $q->where('employee_id', $request->employee_id))
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 30);

        return response()->json($punches);
    }

    public function upload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'data' => 'required|array',
            'data.*.pin' => 'required|string',
            'data.*.punch_at' => 'required|date',
            'data.*.device_serial' => 'nullable|string',
        ]);

        $tenantId = $request->user()->tenant_id;
        $inserted = 0;

        foreach ($validated['data'] as $row) {
            DB::table('raw_punch_data')->insert([
                'tenant_id' => $tenantId,
                'pin' => $row['pin'],
                'punch_date' => $row['punch_at'],
                'device_serial' => $row['device_serial'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $inserted++;
        }

        return response()->json(['message' => "{$inserted} punch records uploaded"], 201);
    }
}
