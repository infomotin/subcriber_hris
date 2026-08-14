<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Calendar;
use App\Models\OtpCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function calendar(Request $request): JsonResponse
    {
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;

        $events = Calendar::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();

        $holidays = $events->filter(fn($e) => $e->is_holiday)->values();
        $workingDays = $events->filter(fn($e) => !$e->is_holiday)->values();

        return response()->json([
            'year' => (int) $year,
            'month' => (int) $month,
            'total_days' => now()->year($year)->month($month)->daysInMonth,
            'holidays' => $holidays,
            'working_days' => $workingDays,
            'events' => $events,
        ]);
    }

    public function verification(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $codes = OtpCode::where('tenant_id', $tenantId)
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        return response()->json(['verification_codes' => $codes]);
    }

    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
        ]);

        $tenantId = $request->user()->tenant_id;
        $otp = OtpCode::where('tenant_id', $tenantId)
            ->where('code', $validated['code'])
            ->where('phone', $validated['phone'])
            ->where('expires_at', '>=', now())
            ->first();

        if (!$otp) {
            return response()->json(['message' => 'Invalid or expired verification code', 'verified' => false], 422);
        }

        $otp->update(['verified_at' => now()]);
        return response()->json(['message' => 'Code verified', 'verified' => true]);
    }
}
