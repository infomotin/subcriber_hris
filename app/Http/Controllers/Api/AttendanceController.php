<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $query = AttendanceLog::with('device')
            ->where('tenant_id', $tenant->id);

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
                'pin' => $log->pin,
                'device' => $log->device?->name ?? 'Unknown',
                'punched_at' => $log->punched_at->format('Y-m-d H:i:s'),
                'status' => $log->status == 0 ? 'IN' : 'OUT',
                'verify_type' => $log->verify_type,
            ];
        });

        return response()->json($logs);
    }

    public function export(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $logs = AttendanceLog::where('tenant_id', $tenant->id)
            ->when($request->date_from, fn($q) => $q->whereDate('punched_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('punched_at', '<=', $request->date_to))
            ->orderBy('punched_at', 'desc')
            ->get();

        $csv = "PIN,Device,Punched At,Status,Verify Type\n";
        foreach ($logs as $log) {
            $csv .= "{$log->pin},{$log->device?->name},{$log->punched_at->format('Y-m-d H:i:s')},";
            $csv .= ($log->status == 0 ? 'IN' : 'OUT') . ",{$log->verify_type}\n";
        }

        return response()->json(['csv' => $csv, 'count' => $logs->count()]);
    }
}
