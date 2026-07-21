<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\Device;
use Illuminate\Http\Request;

class AttendanceLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AttendanceLog::with(['device', 'zktecoUser']);

        if ($request->filled('device_id')) {
            $query->where('device_id', $request->device_id);
        }

        if ($request->filled('pin')) {
            $query->where('pin', 'like', '%' . $request->pin . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('punched_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('punched_at', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->orderBy('punched_at', 'desc')->paginate(25)->withQueryString();
        $devices = Device::all();

        return view('admin.attendance.index', compact('logs', 'devices'));
    }

    public function export(Request $request)
    {
        $query = AttendanceLog::with(['device', 'zktecoUser']);

        if ($request->filled('device_id')) {
            $query->where('device_id', $request->device_id);
        }

        if ($request->filled('pin')) {
            $query->where('pin', 'like', '%' . $request->pin . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('punched_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('punched_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('punched_at', 'desc')->get();

        $csvFileName = 'attendance_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Device Serial', 'User PIN', 'User Name', 'Punched At', 'Status', 'Verify Type']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->device->serial_number ?? 'N/A',
                    $log->pin,
                    $log->zktecoUser->name ?? 'N/A',
                    $log->punched_at->format('Y-m-d H:i:s'),
                    $log->status_label,
                    $log->verify_type_label,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
