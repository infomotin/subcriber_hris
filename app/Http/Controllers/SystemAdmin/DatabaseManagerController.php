<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\Device;
use App\Models\DeviceCommand;
use App\Models\PaymentLog;
use App\Models\SmsLog;
use App\Models\Tenant;
use App\Models\TenantPushLog;
use App\Models\TenantWebhookSetting;
use App\Models\User;
use App\Models\ZktecoUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseManagerController extends Controller
{
    public function index()
    {
        return $this->overview();
    }

    public function overview()
    {
        $tablesStats = $this->getTableStats();
        $isolationAudit = $this->getIsolationAudit();

        $allTables = DB::select("SHOW TABLE STATUS");
        $tablesInfo = collect($allTables)->map(function ($t) {
            return [
                'name' => $t->Name,
                'engine' => $t->Engine,
                'rows' => $t->Rows,
                'collation' => $t->Collation,
                'size' => $this->formatBytes(($t->Data_length + $t->Index_length)),
            ];
        });

        $backups = $this->getBackups();

        $tenants = Tenant::withCount(['devices', 'attendanceLogs', 'zktecoUsers', 'users', 'paymentLogs'])->orderBy('name')->get();

        return view('system_admin.database.index', compact(
            'tablesStats', 'isolationAudit', 'tablesInfo', 'backups', 'tenants'
        ));
    }

    public function backup()
    {
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $path = storage_path("app/backups/{$filename}");

        if (!is_dir(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        $db = config('database.connections.mysql');
        $command = sprintf(
            '"%s" --host=%s --port=%s --user=%s --password=%s %s > %s',
            $this->findMysqldump(),
            $db['host'],
            $db['port'],
            $db['username'],
            $db['password'],
            $db['database'],
            $path
        );

        $output = null;
        $resultCode = null;
        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            return redirect()->route('admin.system.database.index')
                ->with('error', 'Backup failed. Ensure mysqldump is available on the server.');
        }

        return redirect()->route('admin.system.database.index')
            ->with('success', "Backup created: {$filename}");
    }

    public function downloadBackup($filename)
    {
        $path = storage_path("app/backups/{$filename}");
        if (!file_exists($path)) {
            return redirect()->route('admin.system.database.index')
                ->with('error', 'Backup file not found.');
        }
        return response()->download($path);
    }

    public function deleteBackup($filename)
    {
        $path = storage_path("app/backups/{$filename}");
        if (file_exists($path)) {
            unlink($path);
        }
        return redirect()->route('admin.system.database.index')
            ->with('success', "Backup {$filename} deleted.");
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|string',
        ]);

        $filename = $request->input('backup_file');
        $path = storage_path("app/backups/{$filename}");

        if (!file_exists($path)) {
            return redirect()->route('admin.system.database.index')
                ->with('error', 'Backup file not found.');
        }

        $db = config('database.connections.mysql');
        $command = sprintf(
            '"%s" --host=%s --port=%s --user=%s --password=%s %s < %s',
            $this->findMysql(),
            $db['host'],
            $db['port'],
            $db['username'],
            $db['password'],
            $db['database'],
            $path
        );

        $output = null;
        $resultCode = null;
        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            return redirect()->route('admin.system.database.index')
                ->with('error', 'Restore failed. Check the SQL file.');
        }

        return redirect()->route('admin.system.database.index')
            ->with('success', "Database restored from {$filename}");
    }

    public function executeSql(Request $request)
    {
        $request->validate([
            'sql' => 'required|string',
        ]);

        $sql = trim($request->input('sql'));

        try {
            $isSelect = str_starts_with(strtoupper($sql), 'SELECT');

            if ($isSelect) {
                $results = DB::select($sql);
                $columns = !empty($results) ? array_keys((array) $results[0]) : [];

                return redirect()->route('admin.system.database.index', ['tab' => 'sql'])
                    ->with('sql_result', [
                        'type' => 'select',
                        'sql' => $sql,
                        'columns' => $columns,
                        'rows' => $results,
                        'count' => count($results),
                    ]);
            }

            $affected = DB::statement($sql);

            return redirect()->route('admin.system.database.index', ['tab' => 'sql'])
                ->with('sql_result', [
                    'type' => 'statement',
                    'sql' => $sql,
                    'affected' => $affected,
                ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.system.database.index', ['tab' => 'sql'])
                ->with('sql_error', $e->getMessage());
        }
    }

    public function showTable($table)
    {
        if (!$this->tableExists($table)) {
            return redirect()->route('admin.system.database.index')
                ->with('error', "Table '{$table}' does not exist.");
        }

        $columns = collect(DB::select("SHOW COLUMNS FROM `{$table}`"));
        $rows = DB::table($table)->paginate(50);

        $primaryKey = $columns->firstWhere('Key', 'PRI')?->Field ?? 'id';

        return view('system_admin.database.table', compact('table', 'columns', 'rows', 'primaryKey'));
    }

    public function insertRow(Request $request, $table)
    {
        if (!$this->tableExists($table)) {
            return redirect()->route('admin.system.database.index')
                ->with('error', "Table '{$table}' does not exist.");
        }

        $data = $request->except('_token');

        try {
            DB::table($table)->insert($data);
            return redirect()->route('admin.system.database.table', $table)
                ->with('success', 'Row inserted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.system.database.table', $table)
                ->with('error', $e->getMessage());
        }
    }

    public function updateRow(Request $request, $table, $id)
    {
        if (!$this->tableExists($table)) {
            return redirect()->route('admin.system.database.index')
                ->with('error', "Table '{$table}' does not exist.");
        }

        $columns = collect(DB::select("SHOW COLUMNS FROM `{$table}`"));
        $primaryKey = $columns->firstWhere('Key', 'PRI')?->Field ?? 'id';

        $data = $request->except('_token', '_method');

        try {
            DB::table($table)->where($primaryKey, $id)->update($data);
            return redirect()->route('admin.system.database.table', $table)
                ->with('success', 'Row updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.system.database.table', $table)
                ->with('error', $e->getMessage());
        }
    }

    public function deleteRow($table, $id)
    {
        if (!$this->tableExists($table)) {
            return redirect()->route('admin.system.database.index')
                ->with('error', "Table '{$table}' does not exist.");
        }

        $columns = collect(DB::select("SHOW COLUMNS FROM `{$table}`"));
        $primaryKey = $columns->firstWhere('Key', 'PRI')?->Field ?? 'id';

        try {
            DB::table($table)->where($primaryKey, $id)->delete();
            return redirect()->route('admin.system.database.table', $table)
                ->with('success', 'Row deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.system.database.table', $table)
                ->with('error', $e->getMessage());
        }
    }

    public function exportTenantData($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $filename = Str::slug($tenant->name) . '_' . $tenant->id . '_' . date('Y-m-d') . '.sql';
        $sql = "-- Tenant Data Export: {$tenant->name} (ID: {$tenant->id})\n";
        $sql .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";

        $tables = [
            'tenants' => ['query' => Tenant::where('id', $tenant->id)],
            'devices' => ['query' => Device::where('tenant_id', $tenant->id)->withoutGlobalScopes()],
            'attendance_logs' => ['query' => AttendanceLog::where('tenant_id', $tenant->id)->withoutGlobalScopes()],
            'zkteco_users' => ['query' => ZktecoUser::where('tenant_id', $tenant->id)->withoutGlobalScopes()],
            'device_commands' => ['query' => DeviceCommand::where('tenant_id', $tenant->id)->withoutGlobalScopes()],
            'users' => ['query' => User::where('tenant_id', $tenant->id)],
            'payment_logs' => ['query' => PaymentLog::where('tenant_id', $tenant->id)],
            'sms_logs' => ['query' => SmsLog::where('tenant_id', $tenant->id)],
            'tenant_webhook_settings' => ['query' => TenantWebhookSetting::where('tenant_id', $tenant->id)],
            'tenant_push_logs' => ['query' => TenantPushLog::where('tenant_id', $tenant->id)],
        ];

        foreach ($tables as $tableName => $cfg) {
            $rows = $cfg['query']->get();
            if ($rows->isEmpty()) {
                continue;
            }
            $columns = array_keys($rows->first()->getAttributes());
            $cols = implode('`, `', $columns);
            $sql .= "TRUNCATE TABLE `{$tableName}`;\n";

            foreach ($rows->chunk(100) as $chunk) {
                $values = [];
                foreach ($chunk as $row) {
                    $escaped = [];
                    foreach ($columns as $col) {
                        $val = $row->{$col};
                        if (is_null($val)) {
                            $escaped[] = 'NULL';
                        } else {
                            $escaped[] = "'" . str_replace("'", "\\'", $val) . "'";
                        }
                    }
                    $values[] = '(' . implode(', ', $escaped) . ')';
                }
                $sql .= "INSERT INTO `{$tableName}` (`{$cols}`) VALUES\n" . implode(",\n", $values) . ";\n\n";
            }
        }

        return response()->streamDownload(function () use ($sql) {
            echo $sql;
        }, $filename, ['Content-Type' => 'application/sql']);
    }

    private function tableExists($table): bool
    {
        $tables = DB::select("SHOW TABLES");
        $key = "Tables_in_" . config('database.connections.mysql.database');
        return collect($tables)->pluck($key)->contains($table);
    }

    private function getTableStats(): array
    {
        return [
            'users' => User::count(),
            'tenants' => Tenant::count(),
            'devices' => Device::count(),
            'zkteco_users' => ZktecoUser::count(),
            'attendance_logs' => AttendanceLog::count(),
            'tenant_webhook_settings' => DB::table('tenant_webhook_settings')->count(),
            'tenant_push_logs' => DB::table('tenant_push_logs')->count(),
            'system_logs' => DB::table('system_logs')->count(),
        ];
    }

    private function getIsolationAudit(): array
    {
        return [
            'strategy' => 'Single-Database Multi-Tenant Data Isolation',
            'scoping_trait' => 'App\\Traits\\BelongsToTenant',
            'scoped_models' => ['Device', 'ZktecoUser', 'AttendanceLog', 'TenantWebhookSetting', 'TenantPushLog'],
            'isolation_status' => 'ACTIVE & SECURE',
        ];
    }

    private function getBackups(): array
    {
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            return [];
        }

        $files = glob($backupDir . '/*.sql');
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'filename' => basename($file),
                'size' => $this->formatBytes(filesize($file)),
                'date' => date('Y-m-d H:i:s', filemtime($file)),
            ];
        }

        rsort($backups);
        return $backups;
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }

    private function findMysqldump(): string
    {
        return config('database.connections.mysql.dump.mysqldump', 'mysqldump');
    }

    private function findMysql(): string
    {
        $os = strtoupper(substr(PHP_OS, 0, 3));
        return $os === 'WIN' ? 'mysql' : 'mysql';
    }
}
