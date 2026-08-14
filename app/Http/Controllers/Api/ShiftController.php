<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkShift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $shifts = WorkShift::where('tenant_id', $request->user()->tenant_id)
            ->orderBy('name')
            ->get();
        return response()->json($shifts);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'late_buffer_time' => 'nullable|integer|min:0',
        ]);

        $shift = WorkShift::create(array_merge($validated, [
            'tenant_id' => $request->user()->tenant_id,
        ]));

        return response()->json(['message' => 'Shift created', 'shift' => $shift], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $shift = WorkShift::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);
        $shift->update($request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'late_buffer_time' => 'nullable|integer|min:0',
        ]));
        return response()->json(['message' => 'Updated', 'shift' => $shift]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        WorkShift::where('tenant_id', $request->user()->tenant_id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
