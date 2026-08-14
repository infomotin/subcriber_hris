<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kpi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KpiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $kpis = Kpi::with('employee.user')
            ->where('tenant_id', $request->user()->tenant_id)
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 15);

        $kpis->getCollection()->transform(fn($k) => [
            'id' => $k->id,
            'employee' => $k->employee?->user?->name ?? 'N/A',
            'goal_title' => $k->goal_title,
            'target_date' => $k->target_date?->format('Y-m-d'),
            'weightage' => $k->weightage,
            'score' => $k->score_rating,
            'status' => $k->status,
        ]);

        return response()->json($kpis);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_profile_id' => 'required|exists:employee_profiles,id',
            'goal_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_date' => 'nullable|date',
            'weightage' => 'nullable|integer|min:0|max:100',
            'status' => 'nullable|string|max:50',
        ]);

        $kpi = Kpi::create(array_merge($validated, [
            'tenant_id' => $request->user()->tenant_id,
        ]));

        return response()->json(['message' => 'KPI created', 'kpi' => $kpi], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $kpi = Kpi::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);
        $kpi->update($request->validate([
            'goal_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_date' => 'nullable|date',
            'weightage' => 'nullable|integer|min:0|max:100',
            'score_rating' => 'nullable|integer|min:0|max:100',
            'status' => 'nullable|string|max:50',
        ]));
        return response()->json(['message' => 'Updated', 'kpi' => $kpi]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        Kpi::where('tenant_id', $request->user()->tenant_id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
