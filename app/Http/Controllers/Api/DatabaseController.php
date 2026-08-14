<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    public function index(): JsonResponse
    {
        $tables = DB::select('SELECT table_name, table_rows, ROUND(data_length / 1024 / 1024, 2) as size_mb, create_time
            FROM information_schema.tables
            WHERE table_schema = ?
            ORDER BY table_name', [DB::connection()->getDatabaseName()]);

        $stats = DB::select('SELECT
            ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as total_mb,
            COUNT(*) as total_tables
            FROM information_schema.tables
            WHERE table_schema = ?', [DB::connection()->getDatabaseName()]);

        return response()->json([
            'tables' => $tables,
            'stats' => $stats[0] ?? null,
        ]);
    }

    public function backup(): JsonResponse
    {
        $dbName = DB::connection()->getDatabaseName();
        $filename = storage_path("app/backup-{$dbName}-" . now()->format('Y-m-d-Hi') . '.sql');

        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            escapeshellarg(config('database.connections.mysql.username')),
            escapeshellarg(config('database.connections.mysql.password')),
            escapeshellarg(config('database.connections.mysql.host')),
            escapeshellarg($dbName),
            escapeshellarg($filename)
        );

        $output = null;
        $resultCode = null;
        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            return response()->json(['message' => 'Backup failed'], 500);
        }

        return response()->json(['message' => 'Backup created', 'file' => basename($filename)]);
    }

    public function restore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|string',
        ]);

        $filepath = storage_path('app/' . basename($validated['file']));
        if (!file_exists($filepath)) {
            return response()->json(['message' => 'Backup file not found'], 404);
        }

        $dbName = DB::connection()->getDatabaseName();
        $command = sprintf(
            'mysql --user=%s --password=%s --host=%s %s < %s',
            escapeshellarg(config('database.connections.mysql.username')),
            escapeshellarg(config('database.connections.mysql.password')),
            escapeshellarg(config('database.connections.mysql.host')),
            escapeshellarg($dbName),
            escapeshellarg($filepath)
        );

        $output = null;
        $resultCode = null;
        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            return response()->json(['message' => 'Restore failed'], 500);
        }

        return response()->json(['message' => 'Database restored from ' . basename($validated['file'])]);
    }

    public function table(Request $request, $table): JsonResponse
    {
        $columns = DB::select('SHOW COLUMNS FROM `' . $table . '`');
        $data = DB::table($table)->paginate($request->per_page ?? 50);

        return response()->json([
            'table' => $table,
            'columns' => $columns,
            'data' => $data,
        ]);
    }

    public function executeSql(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sql' => 'required|string',
        ]);

        $sql = trim($validated['sql']);
        $forbidden = ['DROP', 'TRUNCATE', 'ALTER', 'CREATE', 'GRANT', 'REVOKE'];
        $upper = strtoupper($sql);
        foreach ($forbidden as $keyword) {
            if (str_starts_with($upper, $keyword)) {
                return response()->json(['message' => "{$keyword} statements are not allowed"], 403);
            }
        }

        try {
            $results = DB::select($sql);
            return response()->json(['results' => $results, 'count' => count($results)]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Query error', 'error' => $e->getMessage()], 400);
        }
    }
}
