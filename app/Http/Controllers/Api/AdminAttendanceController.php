<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = AttendanceLog::with(['device', 'device.tenant']);

        if ($request->tenant_id) {
            $query->where('tenant_id', $request->tenant_id);
        }
        if ($request->date_from) {
            $query->whereDate('punched_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('punched_at', '<=', $request->date_to);
        }
        if ($request->pin) {
            $query->where('pin', $request->pin);
        }

        $logs = $query->orderBy('punched_at', 'desc')->paginate($request->per_page ?? 30);

        $logs->getCollection()->transform(function ($log) {
            return [
                'id' => $log->id,
                'tenant_id' => $log->tenant_id,
                'tenant_name' => $log->device?->tenant?->name ?? 'Unknown',
                'pin' => $log->pin,
                'device' => $log->device?->name ?? 'Unknown',
                'device_serial' => $log->device?->serial_number ?? 'Unknown',
                'punched_at' => $log->punched_at->format('Y-m-d H:i:s'),
                'status' => $log->status == 0 ? 'IN' : 'OUT',
                'verify_type' => $log->verify_type,
            ];
        });

        return response()->json($logs);
    }

    public function export(Request $request): JsonResponse
    {
        $query = AttendanceLog::with('device');

        if ($request->tenant_id) {
            $query->where('tenant_id', $request->tenant_id);
        }
        if ($request->date_from) {
            $query->whereDate('punched_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('punched_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('punched_at', 'desc')->get();

        $csv = "Tenant,PIN,Device,Punched At,Status,Verify Type\n";
        foreach ($logs as $log) {
            $csv .= "{$log->device?->tenant?->name},{$log->pin},{$log->device?->name},";
            $csv .= "{$log->punched_at->format('Y-m-d H:i:s')},";
            $csv .= ($log->status == 0 ? 'IN' : 'OUT') . ",{$log->verify_type}\n";
        }

        return response()->json(['csv' => $csv, 'count' => $logs->count()]);
    }
}
