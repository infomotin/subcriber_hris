<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\Tenant;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        if ($tenant) {
            app()->instance('current_tenant_id', $tenant->id);
            session(['tenant_id' => $tenant->id]);
        }

        $query = AttendanceLog::with(['device', 'zktecoUser']);

        if ($request->filled('pin')) {
            $query->where('pin', $request->pin);
        }

        if ($request->filled('date')) {
            $query->whereDate('punched_at', $request->date);
        }

        $attendanceLogs = $query->orderBy('punched_at', 'desc')->paginate(20);

        return view('subscriber.attendance.index', compact('attendanceLogs'));
    }

    public function export()
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        if ($tenant) {
            app()->instance('current_tenant_id', $tenant->id);
        }

        $logs = AttendanceLog::with(['device', 'zktecoUser'])->orderBy('punched_at', 'desc')->get();

        $filename = 'subscriber_attendance_logs_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'User PIN', 'User Name', 'Device Serial', 'Punched Time', 'Status', 'Verify Type']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->pin,
                    $log->zktecoUser->name ?? 'N/A',
                    $log->device->serial_number ?? 'N/A',
                    $log->punched_at ? $log->punched_at->format('Y-m-d H:i:s') : '',
                    $log->status_label,
                    $log->verify_type_label,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
