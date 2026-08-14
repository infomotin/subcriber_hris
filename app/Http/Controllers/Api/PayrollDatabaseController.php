<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollDatabaseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $tables = DB::select("SELECT table_name, table_rows, create_time
            FROM information_schema.tables
            WHERE table_schema = ? AND table_name LIKE ?
            ORDER BY table_name", [DB::connection()->getDatabaseName(), 'payroll_%']);

        return response()->json($tables);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'table' => 'required|string',
            'data' => 'required|array',
        ]);

        $table = $validated['table'];
        $data = $validated['data'];

        if (!str_starts_with($table, 'payroll_')) {
            return response()->json(['message' => 'Only payroll tables allowed'], 403);
        }

        $id = DB::table($table)->insertGetId($data);
        return response()->json(['message' => 'Record inserted', 'id' => $id], 201);
    }
}
