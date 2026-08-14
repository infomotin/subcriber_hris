<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BizPlanController extends Controller
{
    public function index(): JsonResponse
    {
        $plans = SubscriptionPlan::orderBy('price_monthly')->get()->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'slug' => $p->slug,
            'price_monthly' => $p->price_monthly,
            'price_yearly' => $p->price_yearly,
            'max_devices' => $p->max_devices,
            'max_employees' => $p->max_employees,
            'description' => $p->description,
            'features' => $p->features,
            'status' => $p->status,
            'tenant_count' => $p->tenants()->count(),
        ]);
        return response()->json($plans);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:subscription_plans,slug',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_devices' => 'required|integer|min:1',
            'max_employees' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'status' => 'required|string|in:active,inactive',
        ]);

        $plan = SubscriptionPlan::create($validated);
        return response()->json(['message' => 'Plan created', 'plan' => $plan], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:subscription_plans,slug,' . $id,
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_devices' => 'required|integer|min:1',
            'max_employees' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'status' => 'required|string|in:active,inactive',
        ]);

        $plan->update($validated);
        return response()->json(['message' => 'Plan updated', 'plan' => $plan]);
    }

    public function destroy($id): JsonResponse
    {
        $plan = SubscriptionPlan::findOrFail($id);
        if ($plan->tenants()->count() > 0) {
            return response()->json(['message' => 'Cannot delete plan with active subscribers'], 409);
        }
        $plan->delete();
        return response()->json(['message' => 'Plan deleted']);
    }
}
