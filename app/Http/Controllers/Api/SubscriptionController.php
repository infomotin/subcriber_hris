<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function overview(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        if (!$tenant) {
            return response()->json(['message' => 'No tenant'], 404);
        }

        $plan = $tenant->plan;

        return response()->json([
            'tenant' => [
                'name' => $tenant->name,
                'status' => $tenant->status,
                'expires_at' => $tenant->expires_at?->format('Y-m-d'),
                'is_expired' => $tenant->isExpired(),
            ],
            'plan' => $plan ? [
                'name' => $plan->name,
                'price_monthly' => $plan->price_monthly,
                'price_yearly' => $plan->price_yearly,
                'max_devices' => $plan->max_devices,
                'max_employees' => $plan->max_employees,
                'features' => $plan->features,
            ] : null,
            'usage' => [
                'devices' => $tenant->devices()->count(),
                'device_limit' => $tenant->max_devices,
                'employees' => $tenant->employees()->count(),
                'employee_limit' => $tenant->max_employees,
            ],
        ]);
    }

    public function plans(): JsonResponse
    {
        $plans = SubscriptionPlan::where('status', 'active')->get()->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'price_monthly' => $p->price_monthly,
            'price_yearly' => $p->price_yearly,
            'max_devices' => $p->max_devices,
            'max_employees' => $p->max_employees,
            'description' => $p->description,
            'features' => $p->features,
        ]);

        return response()->json($plans);
    }
}
